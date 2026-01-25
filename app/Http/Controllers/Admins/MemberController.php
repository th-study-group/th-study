<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
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
     * 회원 상세정보
     *
     * @return void
     */
    public function show(string $idx)
    {
        return view('admins.members.show');
    }
}