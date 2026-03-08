<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 유입 통계 레퍼지토리
 */
class TrafficStatRepository
{
    /**
     * 일별 집계 원천 데이터 조회
     */
    public function getDailyAccessRows(string $date): Collection
    {
        return DB::table('access_logs')
            ->selectRaw("
                access_date as stat_date,
                access_page,
                device_type,
                COUNT(*) as total_access_count,
                COUNT(DISTINCT COALESCE(session_id, ip)) as real_access_count
            ")
            ->whereDate('access_date', $date)
            ->groupBy('access_date', 'access_page', 'device_type')
            ->get();
    }

    /**
     * 일별 페이지 통계 upsert
     */
    public function upsertDailyPageStats(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        DB::table('daily_page_stats')->upsert(
            $rows,
            ['stat_date', 'access_page', 'device_type'],
            ['total_access_count', 'real_access_count', 'update_datetime']
        );
    }
}
