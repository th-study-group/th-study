<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteCategoriesRequest;
use App\Http\Resources\Mcp\Tools\NoteCategoriesResource;
use App\Services\Api\NoteCategoriesService;
use Illuminate\Http\Request;

/**
 * 노트 카테고리 MCP API 컨트롤러
 */
class NoteCategoriesController extends Controller
{
    public function __construct(
        private readonly NoteCategoriesService $noteCategoriesService
    ) {}

    /**
     * 노트 카테고리 반환 목록
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(NoteCategoriesRequest $request)
    {
        $data = $request->validated();

        $noteCategories = $this->noteCategoriesService->getNoteCategories($data);

        return response()->json([
            'success' => true,
            'message' => '노트 카테고리 목록 조회 성공',
            'data' => NoteCategoriesResource::collection(
                $noteCategories->items()
            ),
            'pagination' => [
                'current_page' => $noteCategories->currentPage(),
                'per_page' => $noteCategories->perPage(),
                'total' => $noteCategories->total(),
                'last_page' => $noteCategories->lastPage(),
                'has_more' => $noteCategories->hasMorePages(),
                'next_page' => $noteCategories->hasMorePages()
                    ? $noteCategories->currentPage() + 1 : null,
            ],
        ]);
    }
}
