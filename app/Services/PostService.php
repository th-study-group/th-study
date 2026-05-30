<?php

namespace App\Services;

use App\Events\PostHistoryEvent;
use App\Http\Requests\Posts\Admin\StorePostRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Support\RequestIp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 게시글 서비스
 */
class PostService
{
    public function __construct(
        private PostRepository $postRepository
    ) {}

    /**
     * 게시글 생성
     *
     * @param StorePostRequest $request
     * @param string $postType
     * @return Post
     */
    public function create(StorePostRequest $request, string $postType): Post
    {
        $userIdx = Auth::id();
    
        return DB::transaction(function () use ($request, $postType, $userIdx) {
            $payload = [
                'user_idx' => $userIdx,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'post_type' => $postType,
                'create_user_idx' => $userIdx,
            ];

            $forceUseFlagSecretTypes = config('board.force_use_flag_secret_type', []);
            if (!in_array($postType, $forceUseFlagSecretTypes, true)) {
                $payload['use_flag'] = 0;
            }

            $post = $this->postRepository->create($payload);

            event(new PostHistoryEvent(
                postIdx: $post->idx,
                jobType: '등록',
                tableName: $post->getTable(),
                postType: $post->post_type,
                createUserIdx: $userIdx,
                ip: RequestIp::resolve($request),
                userAgent: $request->userAgent()
            ));

            Log::info('[Post][Create] success', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
                'status' => $post->status,
                'use_flag' => $post->use_flag,
                'ip' => RequestIp::resolve($request),
            ]);

            return $post;
        });
    }

    /**
     * 게시글 상세 조회
     *
     * @param string $idx
     * @param string $postType
     * @param string $ip
     * @param string $userAgent
     * @return Post
     */
    public function getByIdxWithHistory(string $idx, string $postType, string $ip, string $userAgent): Post
    {
        $userIdx = Auth::id();
        $post = $this->postRepository->findByIdxAndType($idx, $postType);

        Log::info('[Post][View] 조회 완료', [
            'user_idx' => $userIdx,
            'post_idx' => $post->idx,
            'post_type' => $post->post_type,
            'ip' => $ip,
        ]);

        return $post;
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
     * 게시글 목록
     *
     * @param string $postType
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPosts(string $postType, array $filters): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;

        $posts = $this->postRepository->paginateByType(
            $postType,
            $filters,
            20
        );

        Log::info('[Admin][Post][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'post_type' => $postType,
            'page' => $page,
            'ip' => RequestIp::resolve(),
        ]);

        return $posts;
    }

    /**
     * 공개 게시글 목록
     *
     * @param string $postType
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPublicPosts(string $postType, array $filters): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;
        $filters['use_flag'] = 1;

        $posts = $this->postRepository->paginateByType(
            $postType,
            $filters,
            20
        );

        Log::info('[Post][List] 조회 완료', [
            'user_idx' => Auth::id(),
            'post_type' => $postType,
            'page' => $page,
            'ip' => RequestIp::resolve(),
        ]);

        return $posts;
    }

    /**
     * 공개 게시글 상세 조회
     *
     * @param string $idx
     * @param string $postType
     * @param string $ip
     * @param string $userAgent
     * @return Post
     */
    public function getPublicByIdxWithHistory(string $idx, string $postType, string $ip, string $userAgent): Post
    {
        $userIdx = Auth::id();
        $post = $this->postRepository->findByIdxAndType($idx, $postType);

        if (($post->use_flag ?? 0) != 1) {
            abort(404);
        }

        Log::info('[Post][View] 조회 완료', [
            'user_idx' => $userIdx,
            'post_idx' => $post->idx,
            'post_type' => $post->post_type,
            'ip' => $ip,
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
        $userIdx = Auth::id();

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

            Log::info('[Post][Update] 수정 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
                'ip' => $payload['ip'] ?? '',
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
        $userIdx = Auth::id();
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

            Log::info('[Post][Delete] 삭제 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
                'ip' => $ip,
            ]);
        });
    }

    /**
     * 노출 여부 변경
     *
     * @param Post $post
     * @param array $payload
     * @return Post
     */
    public function updateUseFlag(Post $post, array $payload): Post
    {
        $userIdx = Auth::id();
        $useFlag = (int) ($payload['use_flag'] ?? 0);

        return DB::transaction(function () use ($post, $userIdx, $useFlag, $payload) {
            $post = $this->postRepository->update($post, [
                'use_flag' => $useFlag,
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

            Log::info('[Post][UseFlag] 변경 완료', [
                'user_idx' => $userIdx,
                'post_idx' => $post->idx,
                'post_type' => $post->post_type,
                'use_flag' => $post->use_flag,
                'ip' => $payload['ip'] ?? '',
            ]);

            return $post;
        });
    }
}
