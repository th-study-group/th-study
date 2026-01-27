<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateUserRequest;
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

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 회원가입 목록 (관리자)
     *
     * @return void
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * 회원정보 상세내역 (관리자)
     *
     * @return void
     */
    public function show()
    {
        return view('users.show');
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
        return view('users.password_reset');
    }
}
