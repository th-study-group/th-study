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
    public function index(string $postType)
    {
        return view("{$postType}.index");
    }

    /**
     * 상세내역
     *
     * @return void
     */
    public function show(string $postType)
    {
        return view("{$postType}.show");
    }

    /**
     * 등록 폼
     *
     * @return void
     */
    public function create(string $postType)
    {
        return view("{$postType}.create");
    }

    /**
     * 등록 처리 
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request, string $postType)
    {
    }

    /**
     * 수정 폼
     *
     * @param string $idx
     * @return void
     */
    public function edit(string $postType, string $idx)
    {
        return view("{$postType}.create");
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
