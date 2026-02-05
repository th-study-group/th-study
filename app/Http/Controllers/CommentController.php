<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;

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
    public function show()
    {
        return view('comments.show');
    }

    /**
     * 댓글 등록 처리 
     *
     * @param StoreCommentRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $post = Post::findOrFail($payload['post_idx']);
        $this->authorize('create', [Comment::class, $post]);

        $this->commentService->create([
            'user_idx' => auth()->id(),
            'post_idx' => $payload['post_idx'],
            'content' => $payload['content'],
        ]);

        $postType = $payload['post_type'] ?? $post->post_type;
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('posts.show', [
                'post_type' => $postType,
                'idx' => $payload['post_idx'],
            ]);
        }

        return to_route('inquiries.show', [
            'idx' => $payload['post_idx'],
        ]);
    }

    /**
     * 댓글 수정 처리 
     *
     * @param UpdateCommentRequest $request
     * @param string $idx
     * @return RedirectResponse
     */
    public function update(UpdateCommentRequest $request, string $idx): RedirectResponse
    {
        $comment = $this->commentService->getByIdx($idx);
        $this->authorize('update', $comment);

        $payload = $request->validated();
        $this->commentService->update($comment, [
            'content' => $payload['content'],
            'update_user_idx' => auth()->id(),
        ]);

        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('posts.show', [
                'post_type' => $postType,
                'idx' => $comment->post_idx,
            ]);
        }

        return to_route('inquiries.show', [
            'idx' => $comment->post_idx,
        ]);
    }

    /**
     * 삭제 (soft delete)
     *
     * @param string $idx
     * @return RedirectResponse
     */
    public function destroy(string $idx): RedirectResponse
    {
        $comment = $this->commentService->getByIdx($idx);
        $this->authorize('delete', $comment);

        $this->commentService->delete($comment, [
            'delete_user_idx' => auth()->id(),
        ]);

        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        $routeTypes = config('board.post_type_for_route', []);
        if (!empty($postType) && in_array($postType, $routeTypes, true)) {
            return to_route('posts.show', [
                'post_type' => $postType,
                'idx' => $comment->post_idx,
            ]);
        }

        return to_route('inquiries.show', [
            'idx' => $comment->post_idx,
        ]);
    }
}
