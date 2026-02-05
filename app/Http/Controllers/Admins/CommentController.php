<?php

namespace App\Http\Controllers\Admins;

use App\Http\Requests\Comments\Admin\StoreCommentRequest;
use App\Http\Requests\Comments\Admin\UpdateCommentRequest;
use App\Http\Controllers\Controller;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * 댓글관리
 */
class CommentController extends Controller
{
    public function __construct(
        private CommentService $commentService
    ) {}

    /**
     * 댓글 목록
     *
     * @return void
     */
    public function show() : View
    {
        return view('admins.comments.show');
    }

    /**
     * 댓글 작성 처리 
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        $this->commentService->create([
            'user_idx' => auth()->id(),
            'post_idx' => $payload['post_idx'],
            'content' => $payload['content'],
        ]);

        $postType = $payload['post_type'] ?? null;
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('admins.posts.show', [
                'post_type' => $postType,
                'idx' => $payload['post_idx'],
            ]);
        }

        return to_route('admins.inquiries.show', [
            'idx' => $payload['post_idx'],
        ]);
    }

    /**
     * 댓글 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(UpdateCommentRequest $request, string $idx): RedirectResponse
    {
        $comment = $this->commentService->getByIdx($idx);

        $payload = $request->validated();
        $this->commentService->update($comment, [
            'content' => $payload['content'],
            'update_user_idx' => auth()->id(),
        ]);

        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('admins.posts.show', [
                'post_type' => $postType,
                'idx' => $comment->post_idx,
            ]);
        }

        return to_route('admins.inquiries.show', [
            'idx' => $comment->post_idx,
        ]);
    }

    /**
     * 댓글 삭제 처리 
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx): RedirectResponse
    {
        $comment = $this->commentService->getByIdx($idx);

        $this->commentService->delete($comment, [
            'delete_user_idx' => auth()->id(),
        ]);

        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('admins.posts.show', [
                'post_type' => $postType,
                'idx' => $comment->post_idx,
            ]);
        }

        return to_route('admins.inquiries.show', [
            'idx' => $comment->post_idx,
        ]);
    }
}
