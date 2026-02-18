<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushSentEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $targetUserIdx,
        public int $queued,
        public int $failed,
        public ?int $requestUserIdx = null,
        public ?string $requestIp = null,
    ) {
    }
}
