<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 페이지별 일 통계 자동 삭제 스크립트
 */
class CleanupOldPageStatLogs extends Command
{
    protected $signature = 'logs:cleanup';
    protected $description = '오래된 로그 삭제';

    public function handle()
    {
        // 60일
        // 365일로 변경 (트래픽 증가 시 변경)
        DB::table('access_logs')
            ->where('access_date', '<', now()->subDays(365)->toDateString())
            ->delete();

        // 30일 
        // 365일로 변경 (트래픽 증가 시 변경)
        DB::table('bot_access_logs')
            ->where('access_date', '<', now()->subDays(365)->toDateString())
            ->delete();

        // 90일
        // 500일로 변경 (트래픽 증가 시 변경)
        DB::table('conversion_logs')
            ->where('conversion_date', '<', now()->subDays(500)->toDateString())
            ->delete();

        $this->info('오래된 로그 삭제 완료');

        return self::SUCCESS;
    }
}
