<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteTopicRequest;
use App\Http\Resources\Mcp\Tools\NoteTopicResource;
use App\Services\Api\NoteTopicService;
use Illuminate\Http\JsonResponse;

/**
 * 노트 주제 MCP API 컨트롤러
 */
class NoteTopicController extends Controller
{
    public function __construct(
        private readonly NoteTopicService $noteTopicService
    ) {}

    /**
     * 노트 주제 목록 반환
     *
     * @param NoteTopicRequest $request
     * @return JsonResponse
     */
    public function index(NoteTopicRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteTopics = $this->noteTopicService->getNoteTopics($data);

        return response()->json([
            'success' => true,
            'message' => '노트 주제 목록 조회 성공',
            'data' => NoteTopicResource::collection(
                $noteTopics->items()
            ),
            'pagination' => [
                'current_page' => $noteTopics->currentPage(),
                'per_page' => $noteTopics->perPage(),
                'total' => $noteTopics->total(),
                'last_page' => $noteTopics->lastPage(),
                'has_more' => $noteTopics->hasMorePages(),
                'next_page' => $noteTopics->hasMorePages()
                    ? $noteTopics->currentPage() + 1 : null,
            ],
        ]);
    }
}
