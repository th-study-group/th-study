<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * 글 목록
     *
     * @return void
     */
    public function index()
    {
        return view('posts.index');
    }

    /**
     * 상세내역
     *
     * @return void
     */
    public function show()
    {
        return view('posts.show');
    }

    /**
     * 등록 폼
     *
     * @return void
     */
    public function create()
    {
        return view('posts.create');
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
        return view('posts.edit');
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
