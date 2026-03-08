<?php

namespace App\Services;

use App\Repositories\TrafficLogRepository;
use App\Repositories\TrafficStatRepository;
use App\Support\RequestIp;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

/**
 * 유입/집계 서비스
 */
class TrafficAnalyticsService
{
    public function __construct(
        private TrafficLogRepository $trafficLogRepository,
        private TrafficStatRepository $trafficStatRepository
    ) {}

    /**
     * 유입 원시 로그 저장 (사용자/봇 분기)
     */
    public function trackRequest(Request $request): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent() ?? '');

        $refererUrl = $request->headers->get('referer');
        $pagePath = $this->getPagePath($request);
        $refererHost = $this->resolveRefererHost($refererUrl);
        $clientIp = RequestIp::resolve($request);
        $now = now();

        if ($agent->isRobot()) {
            $this->trafficLogRepository->createBotAccess([
                'access_date' => $now->toDateString(),
                'access_datetime' => $now,
                'access_page' => $pagePath,
                'referer_host' => $refererHost,
                'bot_name' => $agent->robot(),
                'referer_url' => $refererUrl,
                'user_agent' => $request->userAgent() ?? '',
            ]);

            return;
        }

        $deviceInfo = detectDeviceInfo($request->userAgent());
        $user = $request->user();

        $this->trafficLogRepository->createUserAccess([
            'access_date' => $now->toDateString(),
            'access_datetime' => $now,
            'access_page' => $pagePath,
            'referer_host' => $refererHost,
            'device_type' => detectDeviceType($agent),
            'device_brand' => $deviceInfo['device_brand'],
            'device_model' => $deviceInfo['device_model'],
            'os' => $agent->platform(),
            'browser' => detectBrowserName($request->userAgent(), $agent),
            'ip' => $clientIp,
            'referer_url' => $refererUrl,
            'user_agent' => $request->userAgent() ?? '',
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'user_idx' => $user?->idx,
        ]);
    }

    /**
     * 일별 페이지 통계 집계
     *
     * @return int 집계 row 수
     */
    public function aggregateDaily(string $date): int
    {
        $now = now();
        $accessRows = $this->trafficStatRepository->getDailyAccessRows($date);

        $rows = $accessRows->map(function ($row) use ($now) {
            return [
                'stat_date' => $row->stat_date,
                'access_page' => $row->access_page,
                'device_type' => $row->device_type,
                'total_access_count' => (int) $row->total_access_count,
                'real_access_count' => (int) $row->real_access_count,
                'create_datetime' => $now,
                'update_datetime' => $now,
            ];
        })->all();

        $this->trafficStatRepository->upsertDailyPageStats($rows);

        return $accessRows->count();
    }

    private function getPagePath(Request $request): string
    {
        return $request->path() === '/' ? '/' : '/' . ltrim($request->path(), '/');
    }

    private function parseHost(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        return parse_url($url, PHP_URL_HOST) ?: null;
    }

    private function resolveRefererHost(?string $refererUrl): string
    {
        $host = $this->parseHost($refererUrl);
        if (!is_string($host) || $host === '') {
            return 'direct';
        }

        $normalized = strtolower($host);

        // 서버/프록시 IP가 referer_host로 들어오지 않게 차단한다.
        if (in_array($normalized, config('bot.access_log_excluded_ips', []), true)) {
            return 'direct';
        }

        return $normalized;
    }
}
