<?php

namespace App\Services;

use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 댓글 서비스
 */
class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    /**
     * 게시글 댓글 목록
     *
     * @param integer $postIdx
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function getByPostIdx(int $postIdx, int $perPage = 20): LengthAwarePaginator
    {
        return $this->commentRepository->getByPostIdx($postIdx, $perPage);
    }

    /**
     * 댓글 등록
     *
     * @param array $payload
     * @return Comment
     */
    public function create(array $payload): Comment
    {
        return $this->commentRepository->create([
            'user_idx' => $payload['user_idx'],
            'post_idx' => $payload['post_idx'],
            'content' => $payload['content'],
            'create_user_idx' => $payload['user_idx'],
        ]);
    }

    /**
     * 댓글 조회
     *
     * @param integer $idx
     * @return Comment
     */
    public function getByIdx(int $idx): Comment
    {
        return $this->commentRepository->findByIdx($idx);
    }

    /**
     * 댓글 수정
     *
     * @param Comment $comment
     * @param array $payload
     * @return Comment
     */
    public function update(Comment $comment, array $payload): Comment
    {
        return $this->commentRepository->update($comment, [
            'content' => $payload['content'],
            'update_user_idx' => $payload['update_user_idx'],
        ]);
    }

    /**
     * 댓글 삭제
     *
     * @param Comment $comment
     * @param array $payload
     * @return void
     */
    public function delete(Comment $comment, array $payload): void
    {
        $this->commentRepository->update($comment, [
            'delete_user_idx' => $payload['delete_user_idx'],
        ]);

        $comment->delete();
    }
}
