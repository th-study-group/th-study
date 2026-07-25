<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * 스웨거 활성여부(.env)에 따라 스웨거 활성 미들웨어
 */
class SwaggerEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('l5-swagger.enabled')) {
            abort(404);
        }

        if (!Auth::check()) {
            abort(401);
        }

        if (Auth::user()->level !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
