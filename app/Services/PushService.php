<?php

namespace App\Services;

use App\Jobs\SendWebPushJob;
use App\Repositories\UserRepository;
use App\Repositories\WebPushMessageRepository;
use App\Repositories\WebPushSubScriptionRepository;
use Illuminate\Support\Facades\Log;

/**
 * 웹 푸시 서비스
 */
class PushService
{
    public function __construct(
        private UserRepository $userRepository,
        private WebPushSubScriptionRepository $webPushSubScriptionRepository,
        private WebPushMessageRepository $webPushMessageRepository
    ) {}

    /**
     * 구독 저장
     *
     * @param array $data
     * @param int|null $userIdx
     * @param string|null $userAgent
     * @return void
     */
    public function subscribe(array $data, ?int $userIdx, ?string $userAgent): void
    {
        if (!$userIdx) {
            return;
        }

        $this->webPushSubScriptionRepository->upsertByEndpoint([
            'user_idx' => $userIdx,
            'endpoint' => $data['endpoint'],
            'p256dh' => $data['keys']['p256dh'],
            'auth' => $data['keys']['auth'],
            'user_agent' => $userAgent ?? '',
            'last_seen_datetime' => now(),
        ]);

        Log::info('[Push][Subscribe] 완료', [
            'user_idx' => $userIdx,
            'endpoint' => $data['endpoint'],
            'ip' => request()->ip(),
        ]);
    }

    /**
     * 구독 해제
     *
     * @param string $endpoint
     * @param int|null $userIdx
     * @return void
     */
    public function unsubscribe(string $endpoint, ?int $userIdx): void
    {
        if (!$userIdx) {
            return;
        }

        $this->webPushSubScriptionRepository->deleteByEndpointAndUserIdx($endpoint, $userIdx);

        Log::info('[Push][Unsubscribe] 완료', [
            'user_idx' => $userIdx,
            'endpoint' => $endpoint,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * 최근 사용 시각 갱신
     *
     * @param string $endpoint
     * @param int|null $userIdx
     * @return void
     */
    public function ping(string $endpoint, ?int $userIdx): void
    {
        if (!$userIdx) {
            return;
        }

        $this->webPushSubScriptionRepository->touchLastSeen($endpoint, $userIdx);

        Log::info('[Push][Ping] 완료', [
            'user_idx' => $userIdx,
            'endpoint' => $endpoint,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * 구독 존재 여부 확인
     *
     * @param string $endpoint
     * @param int|null $userIdx
     * @return bool
     */
    public function exists(string $endpoint, ?int $userIdx): bool
    {
        if (!$userIdx) {
            return false;
        }

        return $this->webPushSubScriptionRepository->existsByEndpointAndUserIdx($endpoint, $userIdx);
    }

    /**
     * 클릭 추적 후 리다이렉트 URL 반환
     *
     * @param string $token
     * @return string
     */
    public function open(string $token): string
    {
        $message = $this->webPushMessageRepository->findByClickToken($token);
        if (!$message) {
            return '/';
        }

        $this->webPushMessageRepository->markClicked($message);

        Log::info('[Push][Open] 클릭 기록', [
            'user_idx' => $message->user_idx,
            'push_message_idx' => $message->idx,
            'ip' => request()->ip(),
        ]);

        return sanitize_redirect_target($message->target_url ?? '/');
    }

    /**
     * 사용자 대상 푸시 발송
     *
     * @param array $data
     * @param string|null $senderUserAgent
     * @return array
     */
    public function sendToUser(array $data, ?string $senderUserAgent = null): array
    {
        $targetUserIds = normalize_target_user_ids($data);
        if (empty($targetUserIds)) {
            return [
                'ok' => false,
                'msg' => 'no valid target user',
            ];
        }

        $pushEnabledUserIds = $this->userRepository->getPushEnabledUserIds($targetUserIds);
        if (empty($pushEnabledUserIds)) {
            Log::info('[Push][SendToUser] 푸시 수신 동의 사용자 없음', [
                'user_idx' => auth()->id(),
                'target_user_count' => count($targetUserIds),
                'ip' => request()->ip(),
            ]);

            return [
                'ok' => true,
                'msg' => 'skipped_all_opt_out',
                'queued_users' => 0,
            ];
        }

        foreach ($pushEnabledUserIds as $targetUserId) {
            SendWebPushJob::dispatch(
                userId: $targetUserId,
                title: $data['title'],
                body: $data['body'],
                targetUrl: $data['target_url'],
                tableName: $data['table_name'],
                senderUserAgent: $senderUserAgent,
                requestUserIdx: auth()->id(),
                requestIp: request()->ip()
            );
        }

        Log::info('[Push][SendToUser] 큐 등록 완료', [
            'user_idx' => auth()->id(),
            'target_user_count' => count($targetUserIds),
            'push_enabled_user_count' => count($pushEnabledUserIds),
            'ip' => request()->ip(),
        ]);

        return [
            'ok' => true,
            'msg' => 'queued',
            'queued_users' => count($pushEnabledUserIds),
        ];
    }

    /**
     * 사용자 전체 푸시 구독 삭제
     *
     * @param int|null $userIdx
     * @return int
     */
    public function clearSubscriptionsByUserIdx(?int $userIdx): int
    {
        if (!$userIdx) {
            return 0;
        }

        return $this->webPushSubScriptionRepository->deleteByUserIdx($userIdx);
    }
}
