<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * 문의내역 관리  
 */
class InquiryController extends Controller
{
    /**
     * 문의내역 목록
     *
     * @return void
     */
    public function index() : View
    {
        return view('admins.inquiries.index');
    }

    /**
     * 문의 상세 내역
     *
     * @return void
     */
    public function show(string $idx)
    {
        return view('admins.inquiries.show');
    }

    /**
     * 문의 내역 상태 변경 
     */
    public function updateStatus(string $idx)
    {
    }

    /**
     * 문의내역 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}