<?php

namespace App\Repositories\Mcp\Tools;

use App\Models\Note;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 블로그 MCP API 레포지토리
 */
class NoteRepository
{
    /**
     * 블로그 글 목록 반환
     *
     * @return LengthAwarePaginator
     */
    public function paginateNotes(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $notes = Note::query()
            ->select(
                'idx', 'subject', 'content', 'create_datetime', 'create_user_idx',
                'group_idx', 'categories_idx', 'topic_idx',
                'group_code', 'categories_code'
            )
            ->with([
                'group:idx,code,name',
                'category:idx,code,name,use_flag',
                'topic:idx,name,use_flag',
            ])
            ->where('use_flag', 1)
            ->when(!empty($data['subject']), function ($query) use ($data) {
                $query->where('subject', 'like', "%{$data['subject']}%");
            })
            ->when(!empty($data['group_code']), function ($query) use ($data) {
                $query->whereHas('group', function ($q) use ($data) {
                    $q->where('code', $data['group_code']);
                });
            })
            ->when(!empty($data['categories_code']), function ($query) use ($data) {
                $query->whereHas('category', function ($categoriesQuery) use ($data) {
                    $categoriesQuery
                        ->where('code', $data['categories_code'])
                        ->where('use_flag', 1)
                        ->when(!empty($data['group_code']), function ($groupQuery) use ($data) {
                            $groupQuery->whereHas('group', function ($q) use ($data) {
                                $q->where('code', $data['group_code']);
                            });
                        });
                });
            })
            ->when(!empty($data['topic_code']), function ($query) use ($data) {
                $query->whereHas('topic', function ($topicQuery) use ($data) {
                    $topicQuery
                        ->where('name', $data['topic_code'])
                        ->where('use_flag', 1)
                        ->when(!empty($data['categories_code']), function ($categoriesQuery) use ($data) {
                            $categoriesQuery->whereHas('category', function ($q) use ($data) {
                                $q->where('code', $data['categories_code'])
                                  ->where('use_flag', 1)
                                  ->when(!empty($data['group_code']), function ($groupQuery) use ($data) {
                                      $groupQuery->whereHas('group', function ($q) use ($data) {
                                          $q->where('code', $data['group_code']);
                                      });
                                  });
                            });
                        });
                });
            })
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);   

        return $notes;
    }
}