<?php

namespace App\Repositories;

use App\Models\WebPushMessage;

/**
 * 웹 푸시 발송 이력 레퍼지토리
 */
class WebPushMessageRepository
{
    /**
     * 발송 이력 생성
     *
     * @param array $payload
     * @return WebPushMessage
     */
    public function create(array $payload): WebPushMessage
    {
        $message = new WebPushMessage();
        $message->forceFill($payload);
        $message->save();

        return $message;
    }

    /**
     * 발송 이력 다중 등록
     *
     * @param array $rows
     * @return bool
     */
    public function insertMany(array $rows): bool
    {
        if (empty($rows)) {
            return true;
        }

        return WebPushMessage::insert($rows);
    }

    /**
     * 클릭 토큰으로 조회
     *
     * @param string $clickToken
     * @return WebPushMessage|null
     */
    public function findByClickToken(string $clickToken): ?WebPushMessage
    {
        return WebPushMessage::where('click_token', $clickToken)->first();
    }

    /**
     * 클릭 시각 기록
     *
     * @param WebPushMessage $message
     * @return WebPushMessage
     */
    public function markClicked(WebPushMessage $message): WebPushMessage
    {
        if (!$message->click_datetime) {
            $message->click_datetime = now();
            $message->save();
        }

        return $message;
    }
}
