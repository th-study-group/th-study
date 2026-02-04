<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * 댓글 수정 권한
     *
     * @param User $user
     * @param Comment $comment
     * @return boolean
     */
    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_idx === $user->idx;
    }

    /**
     * 댓글 삭제 권한
     *
     * @param User $user
     * @param Comment $comment
     * @return boolean
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_idx === $user->idx;
    }
}
