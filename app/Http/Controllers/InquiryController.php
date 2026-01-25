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
        return view('inquiries.index');
    }

    /**
     * 글 작성 화면
     *
     * @return void
     */
    public function create()
    {
        return view('inquiries.create');
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
        return view('inquiries.show');
    }

    /**
     * 글 수정 화면
     *
     * @return void
     */
    public function edit()
    {
        return view('inquiries.edit');
    }

    /**
     * 글 수정 처리 
     *
     * @param Requst $request
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * 글 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}
