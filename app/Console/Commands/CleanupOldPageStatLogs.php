<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldPageStatLogs extends Command
{
    protected $signature = 'logs:cleanup';
    protected $description = '오래된 로그 정리';

    public function handle()
    {
        // 기본 정책은 30일이지만, 초기 트래픽 데이터 축적 단계라 1000일 기준으로 정리합니다.
        DB::table('access_logs')
            ->where('access_date', '<', now()->subDays(1000)->toDateString())
            ->delete();

        // 기본 정책은 60일이지만, 초기 트래픽 데이터 축적 단계라 1000일 기준으로 정리합니다.
        DB::table('bot_access_logs')
            ->where('access_date', '<', now()->subDays(1000)->toDateString())
            ->delete();

        // 기본 정책은 90일이지만, 초기 트래픽 데이터 축적 단계라 1000일 기준으로 정리합니다.
        DB::table('conversion_logs')
            ->where('conversion_date', '<', now()->subDays(1000)->toDateString())
            ->delete();

        $this->info('오래된 로그 정리 완료');

        return self::SUCCESS;
    }
}
