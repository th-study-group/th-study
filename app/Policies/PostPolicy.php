<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

/**
 * 게시글 권한 정책
 */
class PostPolicy
{
    /**
     * 문의내역 수정 권한
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function inquiryUpdate(User $user, Post $post): bool
    {
        if ($post->post_type !== 'inquiries' || $post->status !== 'wait') {
            return false;
        }

        if ($user->level === 'admin') {
            return true;
        }

        return $post->user_idx === $user->idx;
    }

    /**
     * 문의내역 삭제 권한 
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function inquiryDelete(User $user, Post $post): bool
    {
        return $this->inquiryUpdate($user, $post);
    }

    /**
     * 문의내역 상태 변경 권한
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function inquiryUpdateStatus(User $user, Post $post): bool
    {
        return $user->level === 'admin'
            && $post->post_type === 'inquiries';
    }

    /**
     * 게시글 수정 권한
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function update(User $user, Post $post): bool
    {
        if ($post->post_type === 'inquiries') {
            return false;
        }

        if ($user->level !== 'admin') {
            return false;
        }

        if (($post->use_flag ?? 0) == 1) {
            return false;
        }

        return $post->user_idx === $user->idx;
    }

    /**
     * 게시글 삭제 권한
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    /**
     * 공개여부 상태 수정 권한 
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function updateUseFlag(User $user, Post $post): bool
    {
        if ($post->post_type === 'inquiries') {
            return false;
        }

        if ($user->level !== 'admin') {
            return false;
        }

        return $post->user_idx === $user->idx;
    }
}
