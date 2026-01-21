<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\GuestPostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

/**
 * 미인증 게시판 컨트롤러
 */
class GuestPostController extends Controller
{
    public function __construct(private PostService $postService)
    {
    }

    /**
     * 문의 등록 처리
     *
     * @param GuestPostRequest $request
     * @return JsonResponse
     */
    public function store(GuestPostRequest $request): JsonResponse
    {
        try {
            $this->postService->create($request);
            return response()->json(['result' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'result' => false,
                'errors' => [
                    'status' => ['처리 중 오류가 발생했습니다.'],
                ],
            ], 500);
        }
    }
}
