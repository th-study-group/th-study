<?php

namespace App\Repositories\Mcp\Tools;

use App\Models\NoteTag;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 노트 태그 MCP API 레포지토리
 */
class NoteTagRepository
{
    /**
     * 노트 태그 목록 반환
     *
     * @return LengthAwarePaginator
     */
    public function paginateNoteTags(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $noteTags = NoteTag::query()
            ->select(
                'idx',
                'name',
                'create_datetime',
                'create_user_idx'
            )
            ->with([
                'notes:idx,group_idx,categories_idx,topic_idx,subject,create_datetime,create_user_idx',
                'notes.group:idx,code,name',
                'notes.category:idx,group_idx,code,name,memo,use_flag',
                'notes.topic:idx,categories_idx,name,memo,use_flag',
            ])
            ->when(!empty($data['tag']), function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['tag'] . '%');
            })
            ->when(!empty($data['group_code']), function ($query) use ($data) {
                $query->whereHas('notes.group', function ($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function ($query) use ($data) {
                $query->whereHas('notes.category', function ($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                      ->where('use_flag', true);
                });
            })
            ->when(!empty($data['topic_code']), function ($query) use ($data) {
                $query->whereHas('notes.topic', function ($q) use ($data) {
                    $q->where('name', $data['topic_code'])
                      ->where('use_flag', true);
                });
            })
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);

        return $noteTags;
    }
}
