<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 댓글 레퍼지토리
 */
class CommentRepository
{
    /**
     * 댓글 생성
     *
     * @param array $data
     * @return Comment
     */
    public function create(array $data): Comment
    {
        $comment = new Comment();
        $comment->forceFill($data);
        $comment->save();

        return $comment;
    }

    /**
     * 댓글 조회
     *
     * @param integer $idx
     * @return Comment
     */
    public function findByIdx(int $idx): Comment
    {
        return Comment::with('post')
            ->where('idx', $idx)
            ->firstOrFail();
    }

    /**
     * 댓글 수정
     *
     * @param Comment $comment
     * @param array $data
     * @return Comment
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->forceFill($data);
        $comment->save();

        return $comment;
    }

    /**
     * 게시글 댓글 목록
     *
     * @param integer $postIdx
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function getByPostIdx(int $postIdx, int $perPage): LengthAwarePaginator
    {
        return Comment::with('user')
            ->where('post_idx', $postIdx)
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);
    }
}
