<?php

namespace App\Services\Api;

use App\Repositories\Api\TrafficLogRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 유입 전환 로그 MCP API 서비스
 */
class TrafficLogService
{
    public function __construct(
        private readonly TrafficLogRepository $trafficLogRepository
    ) {}

    /**
     * 사람 유입 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getAccessLogs(array $data) : LengthAwarePaginator
    {
        $accessLogs = $this->trafficLogRepository->paginateAccessLogs($data);

        Log::info('[AccessLogs][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $accessLogs->currentPage(),
                'per_page' => $accessLogs->perPage(),
                'total' => $accessLogs->total(),
                'last_page' => $accessLogs->lastPage(),
            ],
            'count' => $accessLogs->count(),
        ]);

        return $accessLogs;
    }

    /**
     * 봇 유입 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getBotAccessLogs(array $data) : LengthAwarePaginator
    {
        $botAccessLogs = $this->trafficLogRepository->paginateBotAccessLogs($data);

        Log::info('[BotAccessLogs][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $botAccessLogs->currentPage(),
                'per_page' => $botAccessLogs->perPage(),
                'total' => $botAccessLogs->total(),
                'last_page' => $botAccessLogs->lastPage(),
            ],
            'count' => $botAccessLogs->count(),
        ]);

        return $botAccessLogs;
    }
}