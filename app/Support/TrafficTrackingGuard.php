<?php

namespace App\Support;

use App\Models\User;

class TrafficTrackingGuard
{
    /**
     * true면 트래픽 로그를 스킵한다.
     */
    public function shouldSkip(?User $user, ?string $clientIp = null): bool
    {
        if ($user?->level === 'admin') {
            return true;
        }

        if (!is_string($clientIp) || $clientIp === '') {
            return false;
        }

        return in_array($clientIp, config('traffic.access_log_excluded_ips', []), true);
    }
}
