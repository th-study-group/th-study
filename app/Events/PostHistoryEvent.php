<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class PostHistoryEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $postIdx,
        public string $jobType,
        public string $tableName,
        public string $postType,
        public int $createUserIdx,
        public string $ip,
        public string $userAgent,
        public ?string $status = 'wait',
    ) {
        $prefix = (string) Config::get('database.connections.mysql.prefix', '');
        $this->tableName = $prefix . $tableName;
        $this->status = $this->status ?: 'wait';
    }
}
