<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\Admin\StorePostRequest;
use App\Http\Requests\Posts\Admin\PostSearchRequest;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 게시글 관리
 */
class PostController extends Controller 
{
    public function __construct(
        private PostService $postService
    ) {}

    /**
     * 글 목록 조회 
     *
     * @return View
     */
    public function index(PostSearchRequest $request, string $postType) : View
    {
        $filters = $request->validated();
        $posts = $this->postService->getPosts($postType, $filters);
        $posts->appends($filters);

        return view("admins.{$postType}.index", [
            'posts' => $posts,
            'filters' => $filters,
            'useFlagLabels' => config('board.post_use_flag'),
        ]);
    }

    /**
     * 글 작성화면 
     *
     * @return View
     */
    public function create(string $postType) : View
    {
        return view("admins.{$postType}.create");
    }

    /**
     * 글 작성 처리
     *
     * @param StorePostRequest $request
     * @return void
     */
    public function store(StorePostRequest $request, string $postType)
    {
        $post = $this->postService->create($request, $postType);

        return to_route('admins.posts.show', [
            'post_type' => $postType,
            'idx' => $post->idx,
        ]);
    }

    /**
     * 글 상세 화면 
     *
     * @return View
     */
    public function show(string $postType, string $idx)
    {
        $post = $this->postService->getByIdxWithHistory(
            $idx,
            $postType,
            request()->ip(),
            request()->userAgent()
        );

        return view("admins.{$postType}.show", [
            'post' => $post,
            'useFlagLabel' => config('board.post_use_flag')[$post->use_flag ?? 0] ?? '-',
        ]);
    }

    /**
     * 글 수정화면 
     *
     * @param string $idx
     * @return View
     */
    public function edit(string $postType, string $idx) : View
    {
        $post = $this->postService->getByIdx($idx, $postType);
        $this->authorize('update', $post);

        return view("admins.{$postType}.create", [
            'post' => $post,
            'mode' => 'edit',
        ]);
    }

    /**
     * 글 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(StorePostRequest $request, string $postType, string $idx)
    {
        $post = $this->postService->getByIdx($idx, $postType);
        $this->authorize('update', $post);

        $payload = $request->safe()->only(['title', 'content']);
        $payload['ip'] = $request->ip();
        $payload['user_agent'] = $request->userAgent();
        $this->postService->update($payload, $post);

        return to_route('admins.posts.show', [
            'post_type' => $postType,
            'idx' => $post->idx,
        ]);
    }

    /**
     * 글 삭제 처리 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $postType, string $idx)
    {
        $post = $this->postService->getByIdx($idx, $postType);
        $this->authorize('delete', $post);

        $payload = [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        $this->postService->delete($post, $payload);

        return response()->json([
            'message' => '삭제되었습니다.',
        ]);
    }

    /**
     * 글 노출 여부 변경
     */
    public function updateUseFlag(Request $request, string $postType, string $idx)
    {
        $post = $this->postService->getByIdx($idx, $postType);
        $this->authorize('update', $post);

        $current = $post->use_flag ?? 0;
        $payload = [
            'use_flag' => $current === 1 ? 0 : 1,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $updatedPost = $this->postService->updateUseFlag($post, $payload);

        return response()->json([
            'use_flag' => $updatedPost->use_flag,
        ]);
    }
}
