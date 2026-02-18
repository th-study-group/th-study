<?php

namespace App\Jobs;

use App\Events\PushSentEvent;
use App\Repositories\WebPushMessageRepository;
use App\Repositories\WebPushSubScriptionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

/**
 * 웹 푸시 발송 큐 작업
 */
class SendWebPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public string $title,
        public string $body,
        public string $tableName,
        public ?string $targetUrl = null,
        public ?string $senderUserAgent = null,
        public ?int $requestUserIdx = null,
        public ?string $requestIp = null,
    ) {}

    public function handle(
        WebPushSubScriptionRepository $webPushSubScriptionRepository,
        WebPushMessageRepository $webPushMessageRepository
    ): void {
        try {
            $subscriptions = $webPushSubScriptionRepository->getActiveByUserIdx($this->userId);
            if ($subscriptions->isEmpty()) {
                Log::info('[Push][Send][Job] 구독 없음', [
                    'target_user_idx' => $this->userId,
                    'request_user_idx' => $this->requestUserIdx,
                    'ip' => $this->requestIp,
                ]);
                return;
            }

            $client = $this->client();
            $queued = 0;
            $failed = 0;
            $messageRows = [];

            foreach ($subscriptions as $subscriptionRow) {
                $clickToken = Str::random(64);
                $targetUrl = sanitize_redirect_target($this->targetUrl);
                $messageRows[] = [
                    'user_idx' => $this->userId,
                    'endpoint' => $subscriptionRow->endpoint,
                    'title' => $this->title,
                    'body' => $this->body,
                    'click_token' => $clickToken,
                    'target_url' => $targetUrl,
                    'table_name' => $this->tableName,
                    'user_agent' => $this->senderUserAgent ?? '',
                    'send_datetime' => now(),
                    'click_datetime' => null,
                ];

                $payload = json_encode([
                    'title' => $this->title,
                    'body' => $this->body,
                    'url' => '/push/open/' . $clickToken,
                ]);

                $subscription = Subscription::create([
                    'endpoint' => $subscriptionRow->endpoint,
                    'keys' => [
                        'p256dh' => $subscriptionRow->p256dh,
                        'auth' => $subscriptionRow->auth,
                    ],
                ]);

                $client->queueNotification($subscription, $payload);
                $queued++;
            }

            $webPushMessageRepository->insertMany($messageRows);

            foreach ($client->flush() as $report) {
                if ($report->isSuccess()) {
                    continue;
                }

                $endpoint = (string) $report->getRequest()->getUri();
                $webPushSubScriptionRepository->deleteByEndpoint($endpoint);
                $failed++;
            }

            event(new PushSentEvent(
                targetUserIdx: $this->userId,
                queued: $queued,
                failed: $failed,
                requestUserIdx: $this->requestUserIdx,
                requestIp: $this->requestIp
            ));
        } catch (Throwable $e) {
            Log::error('[Push][Send][Job] 실패', [
                'target_user_idx' => $this->userId,
                'request_user_idx' => $this->requestUserIdx,
                'ip' => $this->requestIp,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function client(): WebPush
    {
        return new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.vapid_subject'),
                'publicKey' => config('services.webpush.vapid_public_key'),
                'privateKey' => config('services.webpush.vapid_private_key'),
            ],
        ]);
    }

}
