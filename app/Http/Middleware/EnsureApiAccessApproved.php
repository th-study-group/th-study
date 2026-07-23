<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * API 접근 승인되었는지 체크 미들웨어
 */
class EnsureApiAccessApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => '인증이 필요합니다.',
            ], 401);
        }

        if ($user->api_access_status !== 'approved') {
            return response()->json([
                'message' => 'API 접근 권한이 없습니다.',
            ], 403);
        }

        return $next($request);
    }
}
