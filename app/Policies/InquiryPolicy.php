<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

/**
 * 문의내역 권한 정책
 */
class InquiryPolicy
{
    /**
     * 조회
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function view(User $user, Post $post): bool
    {
        return $post->post_type === 'inquiries'
            &&  $post->user_idx === $user->idx;
    }

    /**
     * 수정
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function update(User $user, Post $post): bool
    {
        return $post->post_type === 'inquiries'
            && $post->user_idx === $user->idx
            && $post->status === 'wait';
    }

    /**
     * 삭제
     *
     * @param User $user
     * @param Post $post
     * @return boolean
     */
    public function delete(User $user, Post $post): bool
    {
        return $post->post_type === 'inquiries'
            && (int) $post->user_idx === (int) $user->idx
            && $post->status === 'wait';
    }
}
