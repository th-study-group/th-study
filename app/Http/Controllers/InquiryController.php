<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * 문의내역
 */
class InquiryController extends Controller
{
    /**
     * 글 목록
     *
     * @return void
     */
    public function index() 
    {
        return view('inquiry.index');
    }

    /**
     * 글 작성 화면
     *
     * @return void
     */
    public function create()
    {
        return view('inquiry.create');
    }

    /**
     * 글 신규 등록
     *
     * @param Request $reuqest
     * @return void
     */
    public function store(Request $reuqest)
    {
    }

    /**
     * 글 상세 화면
     *
     * @return void
     */
    public function show()
    {
        return view('inquiry.show');
    }

    /**
     * 글 수정 화면
     *
     * @return void
     */
    public function edit()
    {
        return view('inquiry.edit');
    }

    /**
     * 글 수정 처리 
     *
     * @return void
     */
    public function update()
    {
    }

    /**
     * 글 삭제 (소프트삭제)
     *
     * @return void
     */
    public function destroy()
    {
    }
}
