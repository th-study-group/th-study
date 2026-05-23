<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\RegisterUserRequest;
use App\Services\EmailVerificationService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

/**
 * 회원가입 컨트롤러
 */
class RegisterController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 회원가입 폼
     *
     * @return void
     */
    public function create()
    {
        if (Auth::check()) {
            return to_route('dashboard');
        }

        return view('users.join');
    }

    /**
     * 사용자 등록 처리
     *
     * @param RegisterUserRequest $request
     * @param EmailVerificationService $evs
     * @return void
     */
    public function register(RegisterUserRequest $request, EmailVerificationService $evs)
    {
        // 디비에 들어가는 값만 필터링 (패스워드만))
        $payload = $request->safe()->except(['password_confirm']);

        $user = $this->userService->register($payload, $request->ip());

        // 이메일 인증 토큰 발급 및 메일 발송
        $evs->issueAndSend($user);

        return to_route('login')
            ->with('status', '회원가입 인증메일이 발송되었습니다. 인증을 진행해주세요.');
    }
}
