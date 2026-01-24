<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * 글 목록
     *
     * @return void
     */
    public function index()
    {
        return view('comments.index');
    }

    /**
     * 상세내역
     *
     * @return void
     */
    public function show()
    {
        return view('comments.show');
    }

    /**
     * 등록 폼
     *
     * @return void
     */
    public function create()
    {
        return view('comments.create');
    }

    /**
     * 등록 처리 
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * 수정 폼
     *
     * @param string $idx
     * @return void
     */
    public function edit(string $idx)
    {
        return view('comments.edit');
    }

    /**
     * 수정 처리 
     *
     * @param Request $reequest
     * @return void
     */
    public function update(Request $reequest)
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
