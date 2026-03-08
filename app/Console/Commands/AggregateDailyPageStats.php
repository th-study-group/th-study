<?php

namespace App\Console\Commands;

use App\Services\TrafficAnalyticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class AggregateDailyPageStats extends Command
{
    public function __construct(
        private TrafficAnalyticsService $trafficAnalyticsService
    ) {
        parent::__construct();
    }

    protected $signature = 'stats:aggregate-daily {date?}';
    protected $description = '일별 페이지 통계 집계';

    public function handle(): int
    {
        $date = $this->argument('date') ?? now()->toDateString();
        Log::info('Daily page stats aggregation started', ['date' => $date]);

        try {
            $rowCount = $this->trafficAnalyticsService->aggregateDaily($date);

            Log::info('Daily page stats aggregation completed', [
                'date' => $date,
                'rows' => $rowCount,
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
