<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoteHistoryEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $noteIdx,
        public string $jobType,
        public int $createUserIdx,
        public string $ip,
        public string $userAgent,
    ) {}
}
