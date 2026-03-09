<?php

namespace App\Repositories;

use App\Models\AccessLog;
use App\Models\BotAccessLog;
use App\Models\ConversionLog;

/**
 * 유입 로그(원시데이터) 레퍼지토리
 */
class TrafficLogRepository
{
    /**
     * 사용자 유입 로그 저장
     */
    public function createUserAccess(array $data): AccessLog
    {
        return AccessLog::create($data);
    }

    /**
     * 봇 유입 로그 저장
     */
    public function createBotAccess(array $data): BotAccessLog
    {
        return BotAccessLog::create($data);
    }

    /**
     * 전환 로그 저장
     */
    public function createConversion(array $data): ConversionLog
    {
        return ConversionLog::create($data);
    }
}
