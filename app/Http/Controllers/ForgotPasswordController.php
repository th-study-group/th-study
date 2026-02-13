<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\ForgotPasswordRequest;
use App\Jobs\SendMailJob;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Throwable;

/**
 * 비밀번호 찾기 
 */
class ForgotPasswordController extends Controller
{
    /**
     * 계정찾기 화면
     *
     * @return void
     */
    public function index(): View
    {
        return view('users.find_account');
    }

    /**
     * 계정 존재 여부에 따라 인증 메일 발송
     *
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse
     */
    public function requestAccountEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->validated();

        $key = 'pw-reset:' . $request->email;
        if (Cache::has($key)) {
            return to_route('password.find.account')
                ->with('status', '안내 메일을 보냈습니다. 메일함을 확인하세요.')
                ->with('cooldown_started', true);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Cache::put($key, true, 300);
            // 계정없는 것을 해커에게 알리지 않기 위함
            return to_route('password.find.account')
                ->with('status', '안내 메일을 보냈습니다. 메일함을 확인하세요.')
                ->with('cooldown_started', true);
        }

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();
        $token = $broker->createToken($user);
        $resetUrl = route('password.reset.mail', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Log::info('Password reset email send', [
                'action' => 'send',
                'model' => 'User',
                'user_idx' => $user->idx,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            SendMailJob::dispatch(
                $user->email,
                new ResetPasswordMail($user->email, $resetUrl),
                '비밀번호찾기',
                $token,
                $user->idx
            );

        } catch (Throwable $e) {
            Log::error('Password reset email send failed', [
                'action' => 'send_failed',
                'model' => 'User',
                'user_idx' => $user->idx,
                'email' => $user->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);
        }

        Cache::put($key, true, 300);

        return to_route('password.find.account')
            ->with('status', '안내 메일을 보냈습니다. 메일함을 확인하세요.')
            ->with('cooldown_started', true);
    }
}
