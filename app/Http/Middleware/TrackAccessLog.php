<?php

namespace App\Http\Middleware;

use App\Support\RequestIp;
use App\Services\TrafficAnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * 사이트 유입 미들웨어 (사람 / 봇 구분)
 */
class TrackAccessLog
{
    public function __construct(
        private TrafficAnalyticsService $trafficAnalyticsService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldTrack($request, $response)) {
            Log::debug('TrackAccessLog skipped', [
                'path' => $request->path(),
                'method' => $request->method(),
                'status' => $response->getStatusCode(),
            ]);
            return $response;
        }

        try {
            $this->trafficAnalyticsService->trackRequest($request);
        } catch (Throwable $e) {
            Log::error('TrackAccessLog failed', [
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
            return $response;
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if ($this->isExcludedPath($request)) {
            return false;
        }

        if ($this->isExcludedAccessLogIp($request)) {
            return false;
        }

        $user = $request->user();
        if ($user && $user->level === 'admin') {
            return false;
        }

        return true;
    }

    private function isExcludedPath(Request $request): bool
    {
        $excludePatterns = [
            'admin*',
            'register',
            'login',
            'logout',
            'email/verify',
            'password/change',
            'find-account',
            'reset-password/*',
            'users',
            'users/*',
            '_debugbar*',
            'build*',
            'css*',
            'js*',
            'images*',
            'img*',
            'storage*',
            'fonts*',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml',
        ];

        foreach ($excludePatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    private function isExcludedAccessLogIp(Request $request): bool
    {
        $excludedIps = config('traffic.access_log_excluded_ips', []);
        if (empty($excludedIps)) {
            return false;
        }

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent() ?? '');

        // 제외 IP는 사람(access_logs) 기록에만 적용하고, 봇 로그는 계속 기록한다.
        if ($agent->isRobot()) {
            return false;
        }

        $clientIp = RequestIp::resolve($request);
        if ($clientIp === '') {
            return false;
        }

        return in_array($clientIp, $excludedIps, true);
    }

}
