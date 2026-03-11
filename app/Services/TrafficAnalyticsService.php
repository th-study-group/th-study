<?php

namespace App\Services;

use App\Repositories\TrafficLogRepository;
use App\Repositories\TrafficStatRepository;
use App\Support\RequestIp;
use App\Support\TrafficTrackingGuard;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Jenssegers\Agent\Agent;

/**
 * 유입/집계 서비스
 */
class TrafficAnalyticsService
{
    public function __construct(
        private TrafficLogRepository $trafficLogRepository,
        private TrafficStatRepository $trafficStatRepository,
        private TrafficTrackingGuard $trafficTrackingGuard
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
        $conversionRows = $this->trafficStatRepository->getDailyConversionRows($date);

        $accessMap = $accessRows->keyBy(function ($row) {
            return $row->stat_date . '|' . $row->access_page . '|' . $row->device_type;
        });

        $conversionMap = $conversionRows->keyBy(function ($row) {
            return $row->stat_date . '|' . $row->access_page . '|' . $row->device_type;
        });

        $allKeys = collect(array_merge(
            $accessMap->keys()->all(),
            $conversionMap->keys()->all()
        ))->unique();

        $rows = $allKeys->map(function ($key) use ($accessMap, $conversionMap, $now) {
            [$statDate, $accessPage, $deviceType] = explode('|', $key);
            $access = $accessMap->get($key);
            $conversion = $conversionMap->get($key);

            return [
                'stat_date' => $statDate,
                'access_page' => $accessPage,
                'device_type' => $deviceType,
                'total_access_count' => (int) ($access->total_access_count ?? 0),
                'real_access_count' => (int) ($access->real_access_count ?? 0),
                'conversion_count' => (int) ($conversion->conversion_count ?? 0),
                'create_datetime' => $now,
                'update_datetime' => $now,
            ];
        })->all();

        $this->trafficStatRepository->upsertDailyPageStats($rows);

        return count($rows);
    }

    /**
     * 전환 원시 로그 저장
     */
    public function trackConversion(Request $request, string $conversionType, ?string $targetPage = null): void
    {
        $this->assertValidConversionType($conversionType);

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent() ?? '');

        $refererUrl = $request->headers->get('referer');
        $now = now();
        $deviceInfo = detectDeviceInfo($request->userAgent());
        $user = $request->user();
        $clientIp = RequestIp::resolve($request);

        if ($this->trafficTrackingGuard->shouldSkip($user, $clientIp)) {
            return;
        }

        $this->trafficLogRepository->createConversion([
            'conversion_date' => $now->toDateString(),
            'conversion_datetime' => $now,
            'access_page' => $this->getPagePath($request),
            'conversion_type' => $conversionType,
            'target_page' => $targetPage,
            'referer_host' => $this->resolveRefererHost($refererUrl),
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
        if (in_array($normalized, config('traffic.access_log_excluded_ips', []), true)) {
            return 'direct';
        }

        return $normalized;
    }

    private function assertValidConversionType(string $conversionType): void
    {
        $types = config('traffic.conversion_types', []);

        if (!in_array($conversionType, $types, true)) {
            throw new InvalidArgumentException("Invalid conversion type: {$conversionType}");
        }
    }
}
