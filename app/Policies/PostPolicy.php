<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
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

    public function inquiryDelete(User $user, Post $post): bool
    {
        return $this->inquiryUpdate($user, $post);
    }

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

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

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

    public function inquiryUpdateStatus(User $user, Post $post): bool
    {
        return $user->level === 'admin'
            && $post->post_type === 'inquiries';
    }
}
