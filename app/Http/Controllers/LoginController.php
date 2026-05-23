<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\LoginUserRequest;
use App\Services\UserService;
use App\Support\RequestIp;
use Illuminate\Support\Facades\Auth;

/**
 * 로그인/로그아웃 컨트롤러
 */
class LoginController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 로그인 폼
     *
     * @return void
     */
    public function login()
    {
        return view('users.login');
    }

    /**
     * 로그인 완료 처리
     *
     * @param LoginUserRequest $request
     * @return void
     */
    public function authenticate(LoginUserRequest $request)
    {
        $payload = $request->safe()->only(['email', 'password']);
        $payload['remember'] = $request->boolean('remember');
        $clientIp = RequestIp::resolve($request);

        $ok = $this->userService->authenticate($payload, $clientIp);

        if (!$ok) {
            return back()
                ->with('status', '이메일 또는 비밀번호가 올바르지 않습니다.')
                ->withInput();
        }

        return redirect()->intended(route('dashboard'))->with('just_logged_in', true);
    }

    /**
     * 로그아웃 처리
     *
     * @return void
     */
    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return to_route('login')->with('just_logged_out', true);
    }
}
