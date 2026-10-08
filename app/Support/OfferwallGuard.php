<?php

namespace App\Support;

use App\Models\User;

class OfferwallGuard
{
    public function shouldBlock(?User $user, ?string $clientIp = null): bool
    {
        if ($user?->level === 'admin') {
            return true;
        }

        if (! is_string($clientIp) || $clientIp === '') {
            return false;
        }

        return in_array($clientIp, config('traffic.access_log_excluded_ips', []), true);
    }
}
