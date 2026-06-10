<?php

namespace App\Repositories\Api;

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
    public function paginateNoteCategories(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $noteCategories = NoteCategory::query()
            ->select(
                'idx', 
                'group_idx', 
                'code', 
                'name', 
                'memo',
                'create_datetime', 
                'create_user_idx'
            )
            ->with([
                'group:idx,code,name',
            ])
            ->where('use_flag', 1)
            ->when(!empty($data['group_code']), function ($query) use ($data) {
                $query->whereHas('group', function ($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function ($query) use ($data) {
                $query->where('code', $data['categories_code']);
            })
            ->when(!empty($data['categories_name']), function ($query) use ($data) {
                $query->where('name', 'like', "%{$data['categories_name']}%");
            })
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);
        
        return $noteCategories;
    }
}