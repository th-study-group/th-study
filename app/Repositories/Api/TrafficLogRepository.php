<?php

namespace App\Repositories\Api;

use App\Models\AccessLog;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 유입 전환 로그 MCP API 레포지토리
 */
class TrafficLogRepository
{
    /**
     * 사람 유입 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateAccessLogs(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $accessLogs = AccessLog::query()
            ->orderby('access_datetime', 'desc')
            ->paginate($perPage);

        return $accessLogs;
    }
}