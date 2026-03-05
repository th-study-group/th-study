<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestIp
{
    /**
     * Resolve client IP from proxy headers and request fallback.
     */
    public static function resolve(?Request $request = null): string
    {
        $request ??= request();

        if (!$request instanceof Request) {
            return '';
        }

        $candidates = [
            self::firstValidIp($request->headers->get('CF-Connecting-IP')),
            self::firstValidIp($request->headers->get('X-Forwarded-For')),
            self::firstValidIp($request->headers->get('X-Real-IP')),
            self::sanitize($request->ip()),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    private static function firstValidIp(?string $raw): string
    {
        if (!$raw) {
            return '';
        }

        foreach (explode(',', $raw) as $segment) {
            $ip = self::sanitize(trim($segment));
            if ($ip !== '') {
                return $ip;
            }
        }

        return '';
    }

    private static function sanitize(?string $ip): string
    {
        if (!$ip) {
            return '';
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
}
