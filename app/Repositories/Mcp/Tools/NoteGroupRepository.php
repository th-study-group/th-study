<?php

namespace App\Repositories\Mcp\Tools;


use App\Models\NoteGroup;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 노트 그룹 MCP API 레포지토리
 */
class NoteGroupRepository
{
    /**
     * 노트 그룹 목록 반환
     *
     * @return LengthAwarePaginator
     */
    public function paginateNoteGroups(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $noteGroups = NoteGroup::query()
            ->select('idx', 'code', 'name', 'create_datetime', 'create_user_idx')
            ->when(!empty($data['group_code']), function ($query) use ($data) {
                $query->where('code', $data['group_code']);
            })
            ->when(!empty($data['group_name']), function ($query) use ($data) {
                $query->where('name', 'like', "%{$data['group_name']}%");
            })
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);

        return $noteGroups;
    }
}