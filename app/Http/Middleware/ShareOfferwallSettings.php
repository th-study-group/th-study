<?php

namespace App\Http\Middleware;

use App\Support\OfferwallGuard;
use App\Support\RequestIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareOfferwallSettings
{
    public function __construct(
        private OfferwallGuard $offerwallGuard
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        View::share('offerwallBlocked', $this->offerwallGuard->shouldBlock(
            $request->user(),
            RequestIp::resolve($request)
        ));

        return $next($request);
    }
}
