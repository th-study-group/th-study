<?php

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Support\Collection;

/**
 * 댓글 레퍼지토리
 */
class CommentRepository
{
    /**
     * 게시글 댓글 목록
     *
     * @param integer $postIdx
     * @return Collection<int, Comment>
     */
    public function getByPostIdx(int $postIdx): Collection
    {
        return Comment::with('user')
            ->where('post_idx', $postIdx)
            ->orderBy('create_datetime')
            ->get();
    }
}
