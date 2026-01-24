<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\RegisterUserRequest;
use App\Http\Requests\Users\LoginUserRequest;
use App\Services\EmailVerificationService;
use App\Services\UserService;
use Illuminate\Http\Request;

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
        return view('users.edit');
    }

    /**
     * 수정 처리 (관리자, 사용자)
     *
     * @param Request $reequest
     * @return void
     */
    public function update(Request $reequest)
    {
    }

    /**
     * 탈퇴화면
     *
     * @return void
     */
    public function withdrawal()
    {
        return view('users.withdrawal');
    }

    /**
     * 삭제 (관리자, 사용자)
     *
     * @return void
     */
    public function destroy()
    {
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
