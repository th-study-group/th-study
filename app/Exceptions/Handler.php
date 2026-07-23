<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 사용자용 공통 에러 페이지 문구 매핑
     *
     * @var array<int, array<string, string>>
     */
    protected array $friendlyErrorPages = [
        403 => [
            'title' => '접근 권한이 없어요',
            'message' => '권한이 있는 계정으로 확인해 주세요',
        ],
        404 => [
            'title' => '페이지를 찾을 수 없어요',
            'message' => '주소를 다시 확인해 주세요',
        ],
        419 => [
            'title' => '세션이 만료되었어요',
            'message' => '다시 로그인한 뒤 시도해 주세요',
        ],
        429 => [
            'title' => '요청이 너무 많아요',
            'message' => '잠시 후 다시 시도해 주세요',
        ],
        500 => [
            'title' => '서버에 문제가 생겼어요',
            'message' => '잠시 후 다시 접속해 주세요',
        ],
        503 => [
            'title' => '잠시 점검 중이에요',
            'message' => '조금 뒤에 다시 접속해 주세요',
        ],
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $context = [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];

            if (! app()->runningInConsole() && app()->bound('request')) {
                $request = app('request');
                $context['ip'] = $request->ip();
                $context['url'] = $request->fullUrl();
            }

            if (app()->bound('log')) {
                app('log')->info('Exception occurred', $context);
            }
        });
    }
}
