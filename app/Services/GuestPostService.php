<?php

namespace App\Services;

use App\Http\Requests\Posts\GuestPostRequest;
use App\Models\GuestPost;
use App\Repositories\GuestPostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 미인증 게시글 서비스 
 */
class GuestPostService
{
    public function __construct(
        private GuestPostRepository $guestPostRepository
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
        $ip = $request->ip();
        $userAgent = (string) $request->userAgent();

        $contactValue = $payload['contact_method'] === 'phone'
            ? $payload['phone']
            : $payload['email'];

        $writer = $payload['name'];
        $title = "{$writer} 님이 메인페이지에서 작성한 문의입니다.";

        Log::info('Guest post create start', [
            'action' => 'create',
            'model' => 'GuestPost',
            'contact_method' => $payload['contact_method'] ?? null,
            'ip' => $ip,
        ]);

        try {
            $guestPost = DB::transaction(function () use ($payload, $ip, $userAgent, $contactValue, $writer, $title) {
                $guestPost = GuestPost::create([
                    'title' => $title,
                    'content' => $payload['inquiry_memo'],
                    'personal_info_agree' => $payload['personal_info_agree'],
                    'marketing_info_agree' => $payload['marketing_info_agree'] ?? 'N',
                    'contact_method' => $payload['contact_method'],
                    'contact_value' => $contactValue,
                    'writer' => $writer,
                    'user_agent' => $userAgent,
                    'ip' => $ip,
                ]);

                $guestPost->forceFill([
                    'post_type' => 'inquiries',
                ])->saveQuietly();

                return $guestPost;
            });

            Log::info('Guest post create success', [
                'action' => 'create',
                'model' => 'GuestPost',
                'guest_post_idx' => $guestPost->idx,
                'ip' => $ip,
            ]);

            return $guestPost;
        } catch (\Throwable $e) {
            Log::error('Guest post create failed', [
                'action' => 'create',
                'model' => 'GuestPost',
                'ip' => $ip,
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
        ]);
    }
}
