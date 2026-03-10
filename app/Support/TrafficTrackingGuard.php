<?php

namespace App\Support;

use App\Models\User;

class TrafficTrackingGuard
{
    /**
     * true면 트래픽 로그를 스킵한다.
     */
    public function shouldSkip(?User $user): bool
    {
        return $user?->level === 'admin';
    }
}
