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
            ->with([
                'user:idx,email',
                'note' => function ($q) {
                    $q->select([
                        'access_page',
                        'group_idx',
                        'categories_idx',
                        'topic_idx',
                        'subject',
                        'content',
                        'thumbnail_path',
                        'use_flag',
                    ])->where('use_flag', 1);
                },

                'note.group' => function ($q) {
                    $q->select('idx', 'code', 'name');
                },

                'note.category' => function ($q) {
                    $q->select('idx', 'group_idx', 'code', 'name', 'use_flag')
                    ->where('use_flag', 1);
                },

                'note.topic' => function ($q) {
                    $q->select('idx', 'categories_idx', 'name', 'memo', 'use_flag')
                    ->where('use_flag', 1);
                },
            ])
            ->select([
                'idx',
                'user_idx',
                'access_page',
                'access_datetime',
                'device_type',
                'device_brand',
                'device_model',
                'os',
                'browser',
                'ip',
                'referer_url',
                'referer_host',
                'user_agent',
                'session_id',
            ])
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
