<?php

namespace App\Listeners;

use App\Events\PushSentEvent;
use Illuminate\Support\Facades\Log;

/**
 * 푸시 발송 로그 기록
 */
class WritePushLogEventListener
{
    public function handle(PushSentEvent $event): void
    {
        Log::info('[Push][Send][Event] 처리 완료', [
            'user_idx' => $event->requestUserIdx,
            'target_user_idx' => $event->targetUserIdx,
            'queued' => $event->queued,
            'failed' => $event->failed,
            'ip' => $event->requestIp,
        ]);
    }
}
