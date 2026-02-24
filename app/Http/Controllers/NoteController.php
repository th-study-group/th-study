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

    /**
     * TinyMCE 이미지 업로드 처리
     * TinyMCE는 { location: '이미지URL' } JSON을 기대함.
     *
     * @param Request $request
     * @return void
     */
    public function uploadImage(Request $request)
    {
        $file = $request->file('file');

        // TinyMCE/브라우저 조합에 따라 키명이 달라지는 케이스 방어
        if (! $file) {
            $allFiles = $request->allFiles();
            if (! empty($allFiles)) {
                $file = array_values($allFiles)[0];
            }
        }

        if ($file instanceof UploadedFile) {
            $request->files->set('file', $file);
        }

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml,image/heic,image/heif,image/avif',
            ],
        ], [
            'file.required' => '업로드 파일이 전달되지 않았습니다.',
            'file.file' => '업로드 파일 형식이 올바르지 않습니다.',
            'file.max' => '이미지 용량은 10MB 이하만 가능합니다.',
            'file.mimetypes' => '지원하지 않는 이미지 형식입니다. (jpg, png, gif, webp, bmp, svg, heic, heif, avif)',
        ]);

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid()->toString() . '.' . $ext;
        $path = $file->storeAs('uploads/notes', $filename, 'public');

        return response()->json([
            'location' => asset('storage/' . $path),
        ]);
    }
}
