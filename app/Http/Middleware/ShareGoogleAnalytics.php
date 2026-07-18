<?php

namespace App\Http\Middleware;

use App\Support\RequestIp;
use App\Support\TrafficTrackingGuard;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * 구글 애널리틱스 미들웨어
 */
class ShareGoogleAnalytics
{
    public function __construct(
        private TrafficTrackingGuard $trafficTrackingGuard
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        View::share('analyticsEnabled', $this->shouldTrack($request));

        return $next($request);
    }

    private function shouldTrack(Request $request): bool
    {
        if (! config('analytics.enabled', false)) {
            return false;
        }

        if (blank(config('analytics.measurement_id'))) {
            return false;
        }

        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $this->isAllowedPath($request)) {
            return false;
        }

        $clientIp = RequestIp::resolve($request);

        if ($this->trafficTrackingGuard->shouldSkip(
            $request->user(),
            $clientIp
        )) {
            return false;
        }

        return true;
    }

    private function isAllowedPath(Request $request): bool
    {
        foreach (config('analytics.allowed_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
