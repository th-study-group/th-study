<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // 라우터 {idx}에는 숫자만 들어오도록 제한
        Route::pattern('idx', '[0-9]+');
        
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api/mcp')
                ->group(base_path('routes/api/mcp.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // 기본 web (홈/정적페이지 같은 최소)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // MCP (OAuth2 보호 리소스, 인증 서버, MCP API)
            Route::middleware('web')
                ->group(base_path('routes/mcp.php'));   

            // 게시판, 댓글, 문의사항 등 고객 관리
            Route::middleware('web')
                ->group(base_path('routes/user.php'));

            // 계정/인증(회원가입/비번재설정/이메일인증 등)
			Route::middleware('web')
                ->group(base_path('routes/auth.php'));
            
            // 컨텐츠항목(정보관리,문서관리)
			Route::middleware(['web', 'note.slug'])
                ->group(base_path('routes/content.php'));

            // 로그인한 유저에게 보이는 항목 
            Route::middleware(['web', 'auth', 'email.verified'])
                ->group(base_path('routes/login.php'));

            // 관리자
            Route::middleware(['web','auth','email.verified','level:admin'])
                ->prefix('admins')
                ->name('admins.')
                ->group(base_path('routes/admin.php'));

            // 개발 테스트
            Route::middleware(['web','local.only'])
	            ->prefix('_dev')
	            ->name('dev.')
	            ->group(base_path('routes/dev.php'));
        });
    }
}
