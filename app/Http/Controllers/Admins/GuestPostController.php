<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 미인증 고객 게시판 컨트롤러 
 */
class GuestPostController extends Controller
{
    /**
     * 글목록
     *
     * @return void
     */
    public function index() 
    {
        return view('admins.guest_posts.index', [
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 수정화면
     *
     * @param string $postType
     * @param string $idx
     * @return void
     */
    public function edit(string $postType, string $idx)
    {
        return view('admins.guest_posts.edit', [
            'idx' => $idx,
            'statusList' => config('board.status'),
        ]);
    }

    /**
     * 수정 처리
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request) 
    {
    }

    /**
     * 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}
