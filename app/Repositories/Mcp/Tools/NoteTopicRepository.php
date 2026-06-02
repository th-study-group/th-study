<?php

namespace App\Repositories\Mcp\Tools;

use App\Models\NoteTopic;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 노트 주제 MCP API 레포지토리
 */
class NoteTopicRepository
{
    /**
     * 노트 주제 목록 반환
     *
     * @return LengthAwarePaginator
     */
    public function paginateNoteTopics(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $noteTopics = NoteTopic::query()
            ->with([
                'category:idx,group_idx,code,name',
                'category.group:idx,code,name'
            ])
            ->select(
                'idx', 
                'name', 
                'memo', 
                'categories_idx', 
                'create_datetime', 
                'create_user_idx'
            )
            ->where('use_flag', 1)
            ->when(!empty($data['topic_code']), function ($query) use ($data) {
                $query->where('name', $data['topic_code']);
            })
            ->when(!empty($data['topic_memo']), function ($query) use ($data) {
                $query->where('memo', 'like', "%{$data['topic_memo']}%");
            })
            ->when(!empty($data['categories_code']), function ($query) use ($data) {
                $query->whereHas('category', function ($q) use ($data) {
                    $q->where('code', $data['categories_code'])
                      ->where('use_flag', 1)
                      ->when(!empty($data['group_code']), function ($groupQuery) use ($data) {
                          $groupQuery->whereHas('group', function ($q) use ($data) {
                              $q->where('code', $data['group_code']);
                          });
                      });
                });
            })
            ->orderby('create_datetime', 'desc')
            ->paginate($perPage);

        return $noteTopics;
    }
}