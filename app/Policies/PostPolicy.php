<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->level === 'admin';
    }

    public function view(User $user, Post $post): bool
    {
        if ($post->post_type !== 'inquiries') {
            return false;
        }

        if ($user->level === 'admin') {
            return true;
        }

        return (int) $post->user_idx === (int) $user->idx;
    }

    public function update(User $user, Post $post): bool
    {
        if ($post->post_type !== 'inquiries' || $post->status !== 'wait') {
            return false;
        }

        if ($user->level === 'admin') {
            return true;
        }

        return $post->user_idx === $user->idx;
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function updateStatus(User $user, Post $post): bool
    {
        return $user->level === 'admin'
            && $post->post_type === 'inquiries';
    }
}
