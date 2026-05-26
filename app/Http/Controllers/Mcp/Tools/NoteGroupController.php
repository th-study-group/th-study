<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteGroupRequest;
use App\Services\Mcp\Tools\NoteGroupService;
use App\Http\Resources\Mcp\Tools\NoteGroupResource;
use Illuminate\Http\JsonResponse;

/**
 * 노트 그룹 컨트롤러
 */
class NoteGroupController extends Controller
{
    public function __construct(
        private readonly NoteGroupService $noteGroupService
    ) {}

    /**
     * 노트 그룹 목록 반환 
     *
     * @param NoteGroupRequest $request
     * @return JsonResponse
     */
    public function index(NoteGroupRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteGroups = $this->noteGroupService->getNoteGroups($data);

        return response()->json([
            'success' => true,
            'message' => '노트 그룹 목록 조회 성공',
            'data' => NoteGroupResource::collection(
                $noteGroups->items()
            ),
            'pagination' => [
                'current_page' => $noteGroups->currentPage(),
                'per_page' => $noteGroups->perPage(),
                'total' => $noteGroups->total(),
                'last_page' => $noteGroups->lastPage(),
                'has_more' => $noteGroups->hasMorePages(),
                'next_page' => $noteGroups->hasMorePages()
                    ? $noteGroups->currentPage() + 1 : null,
            ],
        ]);
    }
}
