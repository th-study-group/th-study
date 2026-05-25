<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteRequest;
use App\Http\Resources\Mcp\Tools\NoteResource;
use App\Services\Mcp\Tools\NoteService;

/**
 * 블로그 MCP API 컨트롤러
 */
class NoteController extends Controller
{
    public function __construct(
        private readonly NoteService $noteService
    ) {}
    
    /**
     * 블로그 글 목록 반환 
     *
     * @param NoteRequest $request
     * @return void
     */
    public function index(NoteRequest $request)
    {
        $data = $request->validated();

        $notes = $this->noteService->getBlogs($data);
    
        return response()->json([
            'success' => true,
            'message' => '노트 목록 조회 성공',
            'data' => NoteResource::collection(
                $notes->items()
            ),
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
                'last_page' => $notes->lastPage(),
                'has_more' => $notes->hasMorePages(),
                'next_page' => $notes->hasMorePages()
                    ? $notes->currentPage() + 1 : null,
            ],
        ]);
    }
}
