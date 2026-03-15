<?php

namespace App\Repositories;

use App\Models\AccessLog;
use App\Models\BotAccessLog;
use App\Models\ConversionLog;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 유입 로그(원시데이터) 레퍼지토리
 */
class TrafficLogRepository
{
    /**
     * 사용자 유입 로그 저장
     */
    public function createUserAccess(array $data): AccessLog
    {
        return AccessLog::create($data);
    }

    /**
     * 봇 유입 로그 저장
     */
    public function createBotAccess(array $data): BotAccessLog
    {
        return BotAccessLog::create($data);
    }

    /**
     * 전환 로그 저장
     */
    public function createConversion(array $data): ConversionLog
    {
        return ConversionLog::create($data);
    }

    /**
     * 관리자 일일 유입 현황 조회
     */
    public function paginateDailyAccessLogs(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $searchDate = $filters['search_date'];
        $searchDevice = $filters['search_device'] ?? null;
        $searchIp = trim($filters['search_ip'] ?? '');
        $searchOrder = $filters['search_order'] ?? 'desc';
        $deviceTypeMap = [
            'pc' => 'desktop',
            'mobile' => 'mobile',
            'tablet' => 'tablet',
        ];

        $query = AccessLog::query()
            ->with(['user:idx,email'])
            ->whereDate('access_datetime', $searchDate);

        $query
            ->when($searchDevice !== null && $searchDevice !== '', function ($q) use ($searchDevice, $deviceTypeMap) {
                $q->where('device_type', $deviceTypeMap[$searchDevice] ?? $searchDevice);
            })
            ->when($searchIp !== '', function ($q) use ($searchIp) {
                $q->where('ip', 'like', $searchIp . '%');
            });

        return $query
            ->orderBy('access_datetime', $searchOrder)
            ->paginate($perPage);
    }
}
