<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AggregateDailyPageStats extends Command
{
    protected $signature = 'stats:aggregate-daily {date?}';
    protected $description = '일별 페이지 통계 집계';

    public function handle(): int
    {
        $date = $this->argument('date') ?? now()->toDateString();
        Log::info('Daily page stats aggregation started', ['date' => $date]);

        try {
            $now = now();

            $accessRows = DB::table('access_logs')
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

            if ($accessRows->isNotEmpty()) {
                DB::table('daily_page_stats')->upsert(
                    $accessRows->map(function ($row) use ($now) {
                        return [
                            'stat_date' => $row->stat_date,
                            'access_page' => $row->access_page,
                            'device_type' => $row->device_type,
                            'total_access_count' => $row->total_access_count,
                            'real_access_count' => $row->real_access_count,
                            'create_datetime' => $now,
                            'update_datetime' => $now,
                        ];
                    })->all(),
                    ['stat_date', 'access_page', 'device_type'],
                    ['total_access_count', 'real_access_count', 'update_datetime']
                );
            }

            Log::info('Daily page stats aggregation completed', [
                'date' => $date,
                'rows' => $accessRows->count(),
            ]);
        } catch (Throwable $e) {
            Log::error('Daily page stats aggregation failed', [
                'date' => $date,
                'message' => $e->getMessage(),
            ]);
            $this->error("집계 실패: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("집계 완료: {$date}");

        return self::SUCCESS;
    }
}
