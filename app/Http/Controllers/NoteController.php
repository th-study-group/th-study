<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * 노트(블로그) 컨트롤러
 */
class NoteController extends Controller
{
    public function __construct()
    {
    }

    /**
     * 글 목록 
     *
     * @param Request $request
     * @param string|null $slug
     * 
     * @return View
     */
    public function index(Request $request, ?string $slug = null) : View
    {
        $noteType = $request->route('group');

        return view("{$noteType}.index");
    }

    /**
     * 상세내역 
     *
     * @param Request $request
     * @param string $slug
     * @param string $idx
     * 
     * @return View 
     */
    public function show(Request $request, string $slug, string $idx) : View
    {
        $noteType = $request->route('group');

        return view("{$noteType}.show");
    }

    /**
     * 등록 폼
     *
     * @param Request $request
     * @param string $slug
     * 
     * @return View
     */
    public function create(Request $request, string $slug) : View
    {
        $noteType = $request->route('group');

        return view("{$noteType}.create");
    }

    /**
     * 등록 처리
     *
     * @param Request $request
     * @param string $idx
     * @return void
     */
    public function store(Request $request, string $idx)
    {
    }
    
    /**
     * 수정 폼
     *
     * @return void
     */
    public function edit(Request $request, string $idx) : View
    {
        $noteType = $request->route('group');

        return view("{$noteType}.create");
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
     * 삭제 처리 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}