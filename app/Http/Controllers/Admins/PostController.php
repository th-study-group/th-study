<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 게시글 관리
 */
class PostController extends Controller 
{
    /**
     * 글 목록 조회 
     *
     * @return View
     */
    public function index(string $postType) : View
    {
        return view("admins.{$postType}.index");
    }

    /**
     * 글 작성화면 
     *
     * @return View
     */
    public function create(string $postType) : View
    {
        return view("admins.{$postType}.create");
    }

    /**
     * 글 작성 처리
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * 글 상세 화면 
     *
     * @return View
     */
    public function show(string $postType, string $idx) : View
    {
        return view("admins.{$postType}.show");
    }

    /**
     * 글 수정화면 
     *
     * @param string $idx
     * @return View
     */
    public function edit(string $postType, string $idx) : View
    {
        return view("admins.{$postType}.create");
    }

    /**
     * 글 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * 글 삭제 처리 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}
