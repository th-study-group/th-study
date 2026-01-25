<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 댓글관리
 */
class CommentController extends Controller
{
    /**
     * 댓글 목록
     *
     * @return void
     */
    public function show() : View
    {
        return view('admins.comments.show');
    }

    /**
     * 댓글 작성 처리 
     */
    public function store(Request $reuqest)
    {
    }

    /**
     * 댓글 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * 댓글 삭제 처리 
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}