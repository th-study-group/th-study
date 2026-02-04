<?php

namespace App\Services;

use App\Events\PostHistoryEvent;
use App\Http\Requests\Inquiries\StoreInquiryRequest;
use App\Jobs\SendMailJob;
use App\Mail\InquiryCreatedMail;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 게시글 서비스
 */
class InquiryService
{
    public function __construct(
        private PostRepository $postRepository
    ) {}

    /**
     * 게시글 생성
     *
     * @param StoreInquiryRequest $request
     * @param string $postType
     * @return Post
     */
    public function create(StoreInquiryRequest $request, string $postType): Post
    {
        $userIdx = auth()->id();

        return DB::transaction(function () use ($request, $postType, $userIdx) {
            $post = $this->postRepository->create([
                'user_idx' => $userIdx,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'post_type' => $postType,
                'create_user_idx' => $userIdx,
            ]);

            event(new PostHistoryEvent(
                postIdx: $post->idx,
                jobType: '등록',
                tableName: $post->getTable(),
                postType: $post->post_type,
                createUserIdx: $userIdx,
                ip: $request->ip(),
                userAgent: $request->userAgent()
            ));

            $this->notifyAdmins($post, $userIdx);

            return $post;
        });
    }

    /**
     * 게시글 목록
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getMyInquiries(array $filters): LengthAwarePaginator
    {
        $userIdx = auth()->id();
        $page = $filters['page'] ?? 1;

        $posts = $this->postRepository->paginateByUserAndType(
            $userIdx,
            'inquiries',
            $filters,
            20
        );

        Log::info('[Inquiry][List] 조회 완료', [
            'user_idx' => $userIdx,
            'post_type' => 'inquiries',
            'page' => $page,
        ]);

        return $posts;
    }

    /**
     * 관리자 문의내역 목록
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getInquiries(array $filters): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;

        $posts = $this->postRepository->paginateByType(
            'inquiries',
            $filters,
            20
        );

        Log::info('[Admin][Inquiry][List] 조회 완료', [
            'user_idx' => auth()->id(),
            'post_type' => 'inquiries',
            'page' => $page,
        ]);

        return $posts;
    }

    /**
     * 게시글 상세 조회
     *
     * @param string $idx
     * @param string $postType
     * @return Post
     */
    public function getByIdx(string $idx, string $postType): Post
    {
        return $this->postRepository->findByIdxAndType($idx, $postType);
    }

    /**
     * 게시글 이력 조회
     *
     * @param string $idx
     * @param string $postType
     * @param string $ip
     * @param string $userAgent
     * @return Post
     */
    public function getByIdxWithHistory(string $idx, string $postType, string $ip, string $userAgent): Post
    {
        $userIdx = auth()->id();
        $post = $this->postRepository->findByIdxAndType($idx, $postType);

        event(new PostHistoryEvent(
            postIdx: $post->idx,
            jobType: '조회',
            tableName: $post->getTable(),
            postType: $post->post_type,
            createUserIdx: $userIdx,
            ip: $ip,
            userAgent: $userAgent
        ));

        Log::info('[Inquiry][View] 조회 완료', [
            'user_idx' => $userIdx,
            'post_idx' => $post->idx,
            'post_type' => $post->post_type,
        ]);

        return $post;
    }

    /**
     * 게시글 수정
     *
     * @param array $payload
     * @param Post $post
     * @return Post
     */
    public function update(array $payload, Post $post): Post
    {
        $userIdx = auth()->id();

        return DB::transaction(function () use ($payload, $post, $userIdx) {
            $post = $this->postRepository->update($post, [
                'title' => $payload['title'] ?? null,
                'content' => $payload['content'] ?? null,
                'update_user_idx' => $userIdx,
            ]);

            event(new PostHistoryEvent(
                postIdx: $post->idx,
                jobType: '수정',
                tableName: $post->getTable(),
                postType: $post->post_type,
                createUserIdx: $userIdx,
                ip: $payload['ip'] ?? '',
                userAgent: $payload['user_agent'] ?? ''
            ));

            Log::info('[Inquiry][Update] 수정 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
            ]);

            return $post;
        });
    }

    /**
     * 게시글 삭제 (soft delete)
     *
     * @param Post $post
     * @param array $payload
     * @return void
     */
    public function delete(Post $post, array $payload): void
    {
        $userIdx = (int) auth()->id();
        $ip = $payload['ip'] ?? '';
        $userAgent = $payload['user_agent'] ?? '';

        DB::transaction(function () use ($post, $userIdx, $ip, $userAgent) {
            $post->forceFill([
                'delete_user_idx' => $userIdx,
            ])->saveQuietly();

            $post->delete();

            event(new PostHistoryEvent(
                postIdx: $post->idx,
                jobType: '삭제',
                tableName: $post->getTable(),
                postType: $post->post_type,
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
                status: null,
            ));

            Log::info('[Inquiry][Delete] 삭제 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
            ]);
        });
    }

    /**
     * 관리자 문의 상태 변경
     *
     * @param Post $post
     * @param array $payload
     * @return Post
     */
    public function updateStatus(Post $post, array $payload): Post
    {
        $userIdx = (int) auth()->id();
        $status = $payload['status'] ?? 'wait';
        $ip = $payload['ip'] ?? '';
        $userAgent = $payload['user_agent'] ?? '';

        return DB::transaction(function () use ($post, $userIdx, $status, $ip, $userAgent) {
            $post->forceFill([
                'status' => $status,
                'update_user_idx' => $userIdx,
            ])->save();

            event(new PostHistoryEvent(
                postIdx: $post->idx,
                jobType: '수정',
                tableName: $post->getTable(),
                postType: $post->post_type,
                createUserIdx: $userIdx,
                ip: $ip,
                userAgent: $userAgent,
                status: $post->status,
            ));

            Log::info('[Admin][Inquiry][Status] 상태 변경 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
                'status' => $post->status,
            ]);

            return $post;
        });
    }

    /**
     * 메일발송 
     *
     * @param Post $post
     * @param integer $writerIdx
     * @return void
     */
    private function notifyAdmins(Post $post, int $writerIdx): void
    {
        $admins = User::where('level', 'admin')
            ->orderBy('idx')
            ->limit(3)
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $subjectTitle = Str::limit($post->title, 20, '...');
        $contentPreview = Str::limit($post->content, 80, '...');
        $link = route('inquiries.show', ['idx' => $post->idx]);

        foreach ($admins as $admin) {
            SendMailJob::dispatch(
                $admin->email,
                new InquiryCreatedMail(
                    title: $subjectTitle,
                    content: $contentPreview,
                    link: $link
                ),
                '문의등록알림',
                null,
                $admin->idx
            );
        }
    }
}
