<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LocalOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		if (!app()->environment('local')) {
			abort(404);
        }

        if (!app()->isLocal()) {
            abort(404);
        }

		$allowedIps = [
			'127.0.0.1', // 로컬
			'::1',// 로컬 IPv6
			'192.168.0.*',// 집 랜/모바일 와이파이
			'192.168.1.*',
			'10.0.*',
			'172.*',
        ];

		$ip = $request->ip();

		foreach ($allowedIps as $pattern) {
			if (fnmatch($pattern,$ip)) {
				return $next($request);
            }
        }

	    abort(404);
    }
}
