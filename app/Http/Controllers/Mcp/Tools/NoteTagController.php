<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteTagRequest;
use App\Http\Resources\Mcp\Tools\NoteTagResource;
use App\Services\Api\NoteTagService;
use Illuminate\Http\JsonResponse;

/**
 * 노트 태그 MCP API 컨트롤러
 */
class NoteTagController extends Controller
{
    public function __construct(
        private readonly NoteTagService $noteTagService
    ) {}

    /**
     * 노트 태그 목록 반환
     *
     * @param NoteTagRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(NoteTagRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteTags = $this->noteTagService->getNoteTags($data);

        return response()->json([
            'success' => true,
            'message' => '노트 태그 목록 조회 성공',
            'data' => NoteTagResource::collection(
                $noteTags->items()
            ),
            'pagination' => [
                'current_page' => $noteTags->currentPage(),
                'per_page' => $noteTags->perPage(),
                'total' => $noteTags->total(),
                'last_page' => $noteTags->lastPage(),
                'has_more' => $noteTags->hasMorePages(),
                'next_page' => $noteTags->hasMorePages()
                    ? $noteTags->currentPage() + 1 : null,
            ],
        ]);
    }
}
