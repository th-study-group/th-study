<?php

namespace App\Services;

use App\Http\Requests\Posts\GuestPostRequest;
use App\Models\GuestPost;
use App\Models\User;
use App\Repositories\GuestPostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 미인증 게시글 서비스 
 */
class GuestPostService
{
    public function __construct(
        private GuestPostRepository $guestPostRepository,
        private PushService $pushService
    ) {}

     /**
      * 게시글 추가
      *
      * @param GuestPostRequest $request
      * @return GuestPost
      */
    public function create(GuestPostRequest $request): GuestPost
    {
        $payload = $request->validated();
        $context = [
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'contact_value' => $payload['contact_method'] === 'phone'
                ? $payload['phone']
                : $payload['email'],
            'writer' => $payload['name'],
        ];
        $context['title'] = "{$context['writer']} 님이 메인페이지에서 작성한 문의입니다.";

        Log::info('Guest post create start', [
            'action' => 'create',
            'model' => 'GuestPost',
            'contact_method' => $payload['contact_method'] ?? null,
            'ip' => $context['ip'],
        ]);

        $refererUrl = $request->headers->get('referer');
        $context['referer_url'] = is_string($refererUrl) && $refererUrl !== ''
            ? mb_substr($refererUrl, 0, 2048)
            : null;

        try {
            $guestPost = DB::transaction(function () use ($payload, $context) {
                $guestPost = new GuestPost();
                $guestPost->forceFill([
                    'title' => $context['title'],
                    'content' => $payload['inquiry_memo'],
                    'personal_info_agree' => $payload['personal_info_agree'],
                    'marketing_info_agree' => $payload['marketing_info_agree'] ?? 'N',
                    'contact_method' => $payload['contact_method'],
                    'contact_value' => $context['contact_value'],
                    'writer' => $context['writer'],
                    'ip' => $context['ip'],
                    'user_agent' => $context['user_agent'],
                    'referer_url' => $context['referer_url'],
                    'post_type' => 'inquiries',
                ]);
                $guestPost->save();

                return $guestPost;
            });

            Log::info('Guest post create success', [
                'action' => 'create',
                'model' => 'GuestPost',
                'guest_post_idx' => $guestPost->idx,
                'ip' => $context['ip'],
            ]);

            $this->notifyAdminsByPush(
                $context['title'],
                $payload['inquiry_memo'],
                $context['user_agent'],
                $guestPost->idx
            );

            return $guestPost;
        } catch (\Throwable $e) {
            Log::error('Guest post create failed', [
                'action' => 'create',
                'model' => 'GuestPost',
                'ip' => $context['ip'],
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * 게시글 목록
     *
     * @param string $postType
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getGuestPosts(string $postType, array $filters): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;

        $posts = $this->guestPostRepository->paginateByType($postType, $filters, 20);

        Log::info('[Admin][GuestPost][List] 조회 완료', [
            'user_idx' => auth()->id(),
            'post_type' => $postType,
            'page' => $page,
            'ip' => request()->ip(),
        ]);

        return $posts;
    }

    /**
     * 특정 idx로 게시글 찾기 
     *
     * @param string $idx
     * @param string $postType
     * @return GuestPost
     */
    public function getByIdx(string $idx, string $postType): GuestPost
    {
        return $this->guestPostRepository->findByIdxAndType($idx, $postType);
    }

    /**
     * 게시글 수정
     *
     * @param GuestPost $post
     * @param array $payload
     * @return GuestPost
     */
    public function update(GuestPost $post, array $payload): GuestPost
    {
        $userIdx = auth()->id();

        $updated = $this->guestPostRepository->update($post, [
            'memo' => $payload['memo'] ?? null,
            'status' => $payload['status'] ?? $post->status,
            'update_user_idx' => $userIdx,
        ]);

        Log::info('[Admin][GuestPost][Update] 수정 완료', [
            'user_idx' => $userIdx,
            'guest_post_idx' => $updated->idx,
            'post_type' => $updated->post_type,
            'status' => $updated->status,
            'ip' => request()->ip(),
        ]);

        return $updated;
    }

    /**
     * 게시글 삭제
     *
     * @param GuestPost $post
     * @return void
     */
    public function delete(GuestPost $post): void
    {
        $userIdx = auth()->id();

        $post->forceFill([
            'delete_user_idx' => $userIdx,
        ])->saveQuietly();

        $post->delete();

        Log::info('[Admin][GuestPost][Delete] 삭제 완료', [
            'user_idx' => $userIdx,
            'guest_post_idx' => $post->idx,
            'post_type' => $post->post_type,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * 관리자 푸시 발송
     *
     * @param string $title
     * @param string $body
     * @param string $userAgent
     * @param int $guestPostIdx
     * @return void
     */
    private function notifyAdminsByPush(string $title, string $body, string $userAgent, int $guestPostIdx): void
    {
        $admins = User::where('level', 'admin')
            ->orderBy('idx')
            ->limit(3)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $targetUrl = route('admins.guest_posts.edit', [
            'post_type' => 'inquiries',
            'idx' => $guestPostIdx,
        ], true);

        $result = $this->pushService->sendToUser([
            'user_ids' => normalize_target_user_ids([
                'user_ids' => $admins->pluck('idx')->all(),
            ]),
            'title' => $title,
            'body' => Str::limit($body, 60, '...'),
            'target_url' => $targetUrl,
            'table_name' => (new GuestPost())->getTable(),
        ], $userAgent);

        Log::info('[GuestPost][Push] 관리자 푸시 요청', [
            'target_user_count' => $admins->count(),
            'result' => $result,
            'ip' => request()->ip(),
        ]);
    }
}
