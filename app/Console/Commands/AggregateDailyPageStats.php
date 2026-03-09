<?php

namespace App\Console\Commands;

use App\Services\TrafficAnalyticsService;
use Illuminate\Console\Command;
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

        try {
            $rowCount = $this->trafficAnalyticsService->aggregateDaily($date);
        } catch (Throwable $e) {
            $this->error("집계 실패: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("집계 완료: {$date}");

        return self::SUCCESS;
    }
}
