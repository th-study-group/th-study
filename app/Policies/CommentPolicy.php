<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

/**
 * 댓글 권한 정책
 */
class CommentPolicy
{
    /**
     * 댓글 등록 권한
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function create(User $user, Post $post): bool
    {
        $postTypeExcluded = config('board.post_type_excluded', []);
        if ($post->post_type === 'inquiries') {
            return false;
        }

        return !in_array($post->post_type, $postTypeExcluded, true);
    }

    /**
     * 댓글 수정 권한
     *
     * @param User $user
     * @param Comment $comment
     * @return boolean
     */
    public function update(User $user, Comment $comment): bool
    {
        $postTypeExcluded = config('board.post_type_excluded', []);
        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        if ($postType === 'inquiries') {
            return false;
        }
        if (in_array($postType, $postTypeExcluded, true)) {
            return false;
        }

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
        $postTypeExcluded = config('board.post_type_excluded', []);
        $postType = $comment->post?->post_type ?? $comment->post()->value('post_type');
        if ($postType === 'inquiries') {
            return false;
        }
        if (in_array($postType, $postTypeExcluded, true)) {
            return false;
        }

        return $comment->user_idx === $user->idx;
    }
}
