<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * 강제 로그아웃 하기 위한 미들웨어
 */
class CheckSessionVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {

            // 세션 버전 값이 없으면(처음 로그인) 초기화
            if (!session()->has('session_version')) {
                session(['session_version' => $user->session_version]);
            }

            if (session('session_version') != $user->session_version) {
                Auth::logout();

                session()->invalidate();
                $request->session()->regenerateToken();

                return to_route('login')->with('status','세션 변동 사항으로 다시 로그인 해주세요.');
            }
        }

        return $next($request);
    }
}
