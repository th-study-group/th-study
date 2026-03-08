<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use App\Models\BotAccessLog;
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
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent() ?? '');
            $refererUrl = $request->headers->get('referer');
            $pagePath = $this->getPagePath($request);

            if ($agent->isRobot()) {
                BotAccessLog::create([
                    'access_date' => now()->toDateString(),
                    'access_datetime' => now(),
                    'access_page' => $pagePath,
                    'referer_host' => $this->parseHost($refererUrl) ?? $request->getHost(),
                    'bot_name' => $agent->robot(),
                    'referer_url' => $refererUrl,
                    'user_agent' => $request->userAgent() ?? '',
                ]);

                return $response;
            }

            $deviceInfo = detectDeviceInfo($request->userAgent());
            $user = $request->user();

            AccessLog::create([
                'access_date' => now()->toDateString(),
                'access_datetime' => now(),
                'access_page' => $pagePath,
                'referer_host' => $this->parseHost($refererUrl) ?? $request->getHost(),
                'device_type' => detectDeviceType($agent),
                'device_brand' => $deviceInfo['device_brand'],
                'device_model' => $deviceInfo['device_model'],
                'os' => $agent->platform(),
                'browser' => detectBrowserName($request->userAgent(), $agent),
                'ip' => $request->ip(),
                'referer_url' => $refererUrl,
                'user_agent' => $request->userAgent() ?? '',
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'user_idx' => $user?->idx,
            ]);
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

}
