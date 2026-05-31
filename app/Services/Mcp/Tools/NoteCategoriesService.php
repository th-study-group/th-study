<?php

namespace App\Services\Mcp\Tools;

use App\Repositories\Mcp\Tools\NoteCategoriesRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * 노트 카테고리 MCP API 서비스
 */
class NoteCategoriesService
{
    public function __construct(
        private readonly NoteCategoriesRepository $noteCategoriesRepository
    ) {}
    
    /**
     * 노트 카테고리 반환 목록
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getNoteCategories(array $data) : LengthAwarePaginator
    {
        $noteCategories = $this->noteCategoriesRepository->paginateNoteCategories($data);

        Log::info('[NoteCategory][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $noteCategories->currentPage(),
                'per_page' => $noteCategories->perPage(),
                'total' => $noteCategories->total(),
                'last_page' => $noteCategories->lastPage(),
            ],
            'count' => $noteCategories->count(),
        ]);

        return $noteCategories;
    }
}