<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateUserRequest;
use App\Services\PushService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * 사용자 컨트롤러
 */
class UserController extends Controller
{
    private UserService $userService;
    private PushService $pushService;

    public function __construct(UserService $userService, PushService $pushService)
    {
        $this->userService = $userService;
        $this->pushService = $pushService;
    }

    /* 수정 폼 
     *
     * @return void
     */
    public function edit()
    {
        try {
            $user = $this->userService->findById(auth()->user()->idx);

            return view('users.edit', [
                'user' => $user
            ]);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    /**
     * 수정 처리 (관리자, 사용자)
     *
     * @param UpdateUserRequest $request
     * @return void
     */
    public function update(UpdateUserRequest $request)
    {
        $payload = $request->safe()->all();
        $payload['user_idx'] = $request->user()->idx;
        $payload['ip'] = $request->ip();

        Log::info('User update validation passed', [
            'action' => 'validate',
            'model' => 'User',
            'user_idx' => $request->user()?->idx,
            'email' => $request->user()?->email,
            'ip' => $request->ip(),
            'payload' => $payload,
        ]);

        $this->userService->update($payload);

        return to_route('users.account.edit');
    }

    /**
     * 탈퇴화면
     *
     * @return void
     */
    public function withdrawal(Request $request)
    {
        $this->authorize('withdraw', $request->user());

        return view('users.withdrawal');
    }

    /**
     * 삭제 (사용자)
     *
     * @return void
     */
    public function destroy(Request $request)
    {
        $this->authorize('withdraw', $request->user());

        $withdrawalConfirm = $request->input('withdrawal_confirm');

        if (!$withdrawalConfirm) {
            return to_route('users.account.withdrawal')
                ->withErrors([
                    'withdrawal_confirm' => '회원탈퇴 동의가 필요합니다.'
                ])->withInput();
        }

        if (!in_array($withdrawalConfirm, ['Y', 'N'], true)) {
            return to_route('users.account.withdrawal')
                ->withErrors([
                    'withdrawal_confirm' => '유효하지 않은 값입니다.'
                ])->withInput();
        }

        if ($withdrawalConfirm !== 'Y') {
            return to_route('users.account.withdrawal')
                ->withErrors([
                    'withdrawal_confirm' => '회원탈퇴 동의가 필요합니다.'
                ])->withInput();
        }

        $this->userService->withdraw([
            'user_idx' => $request->user()->idx,
            'ip' => $request->ip(),
        ]);
        $this->pushService->clearSubscriptionsByUserIdx($request->user()->idx);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('status', '회원탈퇴가 정상적으로 되었습니다.');
    }

    /**
     * 비밀번호 변경을 위해 동의 화면 
     *
     * @return void
     */
    public function passwordReset() 
    {
        return view('users.password_request');
    }

    /**
     * 비밀번호 변경을 위한 메일 요청 
     *
     * @param Request $request
     * @return void
     */
    public function passwordResetRequest(Request $request) 
    {
        $passwordResetConfirm = $request->input('password_reset_confirm');

        if (!$passwordResetConfirm) {
            return to_route('users.account.password_reset')
                ->withErrors([
                    'password_reset_confirm' => '비밀번호 변경 동의가 필요합니다.'
                ])->withInput();
        }

        if (!in_array($passwordResetConfirm, ['Y', 'N'], true)) {
            return to_route('users.account.password_reset')
                ->withErrors([
                    'password_reset_confirm' => '유효하지 않은 값입니다.'
                ])->withInput();
        }

        if ($passwordResetConfirm !== 'Y') {
            return to_route('users.account.password_reset')
                ->withErrors([
                    'password_reset_confirm' => '비밀번호 변경 동의가 필요합니다.'
                ])->withInput();
        }

        $ok = $this->userService->requestPasswordChange([
            'user_idx' => $request->user()->idx,
            'ip' => $request->ip(),
        ]);

        if (!$ok) {
            return to_route('users.account.password_reset')
                ->with('status', '비밀번호 변경 요청에 실패했습니다. 잠시 후 다시 시도해 주세요.')
                ->withInput();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')
                ->with('status', '비밀번호 변경 요청이 접수되었습니다. 메일을 확인해 주세요.');
    }
}
