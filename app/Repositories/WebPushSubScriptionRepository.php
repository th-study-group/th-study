<?php

namespace App\Repositories;

use App\Models\WebPushSubScription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * 웹 푸시 구독 레퍼지토리
 */
class WebPushSubScriptionRepository
{
    /**
     * endpoint 기준 구독 저장/갱신
     *
     * @param array $payload
     * @return WebPushSubScription
     */
    public function upsertByEndpoint(array $payload): WebPushSubScription
    {
        $subscription = WebPushSubScription::where('endpoint', $payload['endpoint'])->first();

        if (!$subscription) {
            $subscription = new WebPushSubScription();
            $subscription->create_datetime = now();
        }

        $subscription->forceFill([
            'user_idx' => $payload['user_idx'],
            'endpoint' => $payload['endpoint'],
            'p256dh' => $payload['p256dh'],
            'auth' => $payload['auth'],
            'user_agent' => $payload['user_agent'] ?? '',
            'last_seen_datetime' => $payload['last_seen_datetime'] ?? now(),
        ]);
        $subscription->save();

        return $subscription;
    }

    /**
     * 사용자 endpoint 구독 삭제
     *
     * @param string $endpoint
     * @param int $userIdx
     * @return int
     */
    public function deleteByEndpointAndUserIdx(string $endpoint, int $userIdx): int
    {
        return WebPushSubScription::where('endpoint', $endpoint)
            ->where('user_idx', $userIdx)
            ->delete();
    }

    /**
     * endpoint 기준 구독 삭제
     *
     * @param string $endpoint
     * @return int
     */
    public function deleteByEndpoint(string $endpoint): int
    {
        return WebPushSubScription::where('endpoint', $endpoint)->delete();
    }

    /**
     * 사용자 전체 구독 삭제
     *
     * @param int $userIdx
     * @return int
     */
    public function deleteByUserIdx(int $userIdx): int
    {
        return WebPushSubScription::where('user_idx', $userIdx)->delete();
    }

    /**
     * 사용자 endpoint 최근 사용 시각 갱신
     *
     * @param string $endpoint
     * @param int $userIdx
     * @return int
     */
    public function touchLastSeen(string $endpoint, int $userIdx): int
    {
        return WebPushSubScription::where('endpoint', $endpoint)
            ->where('user_idx', $userIdx)
            ->update(['last_seen_datetime' => now()]);
    }

    /**
     * 사용자 endpoint 존재 여부
     *
     * @param string $endpoint
     * @param int $userIdx
     * @return bool
     */
    public function existsByEndpointAndUserIdx(string $endpoint, int $userIdx): bool
    {
        return $this->activeQueryByUserIdx($userIdx)
            ->where('endpoint', $endpoint)
            ->exists();
    }

    /**
     * 사용자 활성 구독 목록 조회 (1분 이내)
     *
     * @param int $userIdx
     * @return Collection<int, WebPushSubScription>
     */
    public function getActiveByUserIdx(int $userIdx): Collection
    {
        return $this->activeQueryByUserIdx($userIdx)
            ->orderByDesc('last_seen_datetime')
            ->get();
    }

    /**
     * 사용자 활성 구독 공통 쿼리
     *
     * @param int $userIdx
     * @return Builder
     */
    private function activeQueryByUserIdx(int $userIdx): Builder
    {
        return WebPushSubScription::where('user_idx', $userIdx)
            ->where('last_seen_datetime', '>=', now()->subYear());
    }
}
