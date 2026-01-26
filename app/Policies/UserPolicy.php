<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * 사용자 권한 정책 
 */
class UserPolicy
{
    /**
     * 탈퇴 권한
     *
     * @param User $user
     * @return boolean
     */
    public function withdraw(User $user): bool
    {
        return $user->level === 'normal';
    }
}
