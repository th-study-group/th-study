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

    /**
     * 사용자 등록 화면 (관리자)
     *
     * @return void
     */
    public function create()
    {
        return view('users.create');
    }

     /**
     * 사용자 등록 처리
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
    }

    /* 수정 폼 
     *
     * @param integer $idx
     * @return void
     */
    public function edit(int $idx)
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
     * 삭제 (관리자, 사용자)
     *
     * @param integer $idx
     * @return void
     */
    public function destroy(int $idx)
    {
    }
}
