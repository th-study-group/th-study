<?php

namespace App\Http\Controllers;

use App\Events\MailSentEvent;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * 계정 찾아서 비밀번호 변경 
 */
class ResetPasswordController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * 비밀번호 인증 메일에서 들어오는 재설정 화면
     *
     * @param string $token
     * @param Request $request
     * @return void
     */
    public function index(string $token, Request $request) : View
    {
        $email = $request->query('email');

        if ($email) {
            event(new MailSentEvent(
                kind: '비밀번호찾기',
                email: $email,
                token: $token,
                sender: null,
                receiveIp: $request->ip(),
                receiveDatetime: now()->toDateTimeString(),
            ));
        }

        return view('users.home_password_change',[
            'token' => $token,
			'email' => $email,
        ]);
    }

    /**
     * 비밀번호 인증 메일 통해서 비밀번호 변경 처리
     *
     * @param ResetPasswordRequest $request
     * @return RedirectResponse
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $this->userService->changePassword($user, $request->password, $request->ip(), [
                    'change_password_flag' => 0,
                    'session_version' => $user->session_version + 1
                ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Log::info('Password reset success', [
                'action' => 'reset_success',
                'model' => 'User',
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return to_route('login')
                ->with('status', '비밀번호 재설정 완료되었습니다.');
        }

        Log::warning('Password reset failed', [
            'action' => 'reset_failed',
            'model' => 'User',
            'email' => $request->email,
            'ip' => $request->ip(),
            'status' => $status,
        ]);

        return to_route('password.reset.mail', [
            'token' => $request->token,
            'email' => $request->email,
        ])->withErrors(['email' => '토큰이 만료되었거나 잘못되었습니다.'])
            ->withInput();
    }
}
