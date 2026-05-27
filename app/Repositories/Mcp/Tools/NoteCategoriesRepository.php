<?php

namespace App\Repositories\Mcp\Tools;

use App\Models\NoteCategory;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 노트 카테고리 MCP API 레포지토리
 */
class NoteCategoriesRepository
{
    /**
     * 노트 카테고리 목록 반환
     *
     * @return LengthAwarePaginator
     */
    public function paginateCategories(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $noteCategories = NoteCategory::query()
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);
        
        return $noteCategories;
    }
}