<?php

namespace App\Repositories;

use App\Models\NoteCategory;
use App\Models\NoteTopic;
use Illuminate\Database\Eloquent\Collection;

/**
 * 노트 주제 레퍼지토리
 */
class NoteTopicRepository
{
    /**
     * 그룹 기준 카테고리 목록 조회
     *
     * @param string $groupCode
     * @return Collection<int, NoteCategory>
     */
    public function getCategoriesByGroupCode(string $groupCode): Collection
    {
        return NoteCategory::with('group')
            ->where('use_flag', 1)
            ->whereHas('group', function ($query) use ($groupCode) {
                $query->where('code', $groupCode);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * 그룹/카테고리 기준 사용중인 주제 목록 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @return Collection<int, NoteTopic>
     */
    public function getActiveTopicsByGroupAndCategory(string $groupCode, string $categoryCode): Collection
    {
        return NoteTopic::with(['category.group'])
            ->where('use_flag', 1)
            ->whereHas('category', function ($query) use ($groupCode, $categoryCode) {
                $query->where('code', $categoryCode)
                    ->whereHas('group', function ($groupQuery) use ($groupCode) {
                        $groupQuery->where('code', $groupCode);
                    });
            })
            ->orderBy('name')
            ->get();
    }
}
