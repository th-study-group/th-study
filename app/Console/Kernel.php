<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 오늘 데이터 5분마다 재집계
        $schedule->command('stats:aggregate-daily')
            ->everyFiveMinutes();

        // 전날 확정 집계
        $schedule->command('stats:aggregate-daily ' . now()->subDay()->toDateString())
            ->dailyAt('00:10');

        // 오래된 로그 삭제
        $schedule->command('logs:cleanup')
            ->dailyAt('04:40');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
