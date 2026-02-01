<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 회원관리 
 */
class MemberController extends Controller
{
    /**
     * 회원목록
     *
     * @return void
     */
    public function index() : View
    {
        return view('admins.members.index');
    }

    /**
     * 회원 상세정보 및 수정
     *
     * @return void
     */
    public function edit(string $idx) : View
    {
        return view('admins.members.edit', [
            'idx' => $idx,
        ]);
    }

    /**
     * 관리자 수정 처리
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
    }
    
}