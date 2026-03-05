<?php

namespace App\Repositories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 노트 레퍼지토리
 */
class NoteRepository
{
    /**
     * 노트 생성
     *
     * @param array $data
     * @return Note
     */
    public function create(array $data): Note
    {
        $note = new Note();
        $note->forceFill($data);
        $note->save();

        return $note;
    }

    /**
     * 노트 수정
     *
     * @param Note $note
     * @param array $data
     * @return Note
     */
    public function update(Note $note, array $data): Note
    {
        $note->forceFill($data);
        $note->save();

        return $note;
    }

    /**
     * 노트 상세 조회
     *
     * @param int $idx
     * @param string $groupCode
     * @param string $categoryCode
     * @return Note
     */
    public function findByIdxAndCodes(int $idx, string $groupCode, string $categoryCode): Note
    {
        $note = Note::with(['tags', 'category', 'group', 'topic'])
            ->where('idx', $idx)
            ->where('group_code', $groupCode)
            ->where('categories_code', $categoryCode)
            ->firstOrFail();

        return $note;
    }

    /**
     * 노트 목록 조회
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @param bool $isAdmin
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByCodes(
        string $groupCode,
        ?string $categoryCode,
        bool $isAdmin,
        int $perPage = 10,
        ?string $searchType = null,
        ?string $searchKeyword = null
    ): LengthAwarePaginator
    {
        return Note::with(['category', 'group', 'topic', 'tags'])
            ->where('group_code', $groupCode)
            ->when(
                is_string($categoryCode) && trim($categoryCode) !== '',
                function ($query) use ($categoryCode) {
                    $query->where('categories_code', trim((string) $categoryCode));
                }
            )
            ->when(! $isAdmin, function ($query) {
                $query->where('use_flag', 1);
            })
            ->when(
                is_string($searchKeyword) && trim($searchKeyword) !== '',
                function ($query) use ($searchType, $searchKeyword) {
                    $keyword = trim((string) $searchKeyword);
                    $type = in_array($searchType, ['title', 'content'], true) ? $searchType : 'title';
                    $column = $type === 'content' ? 'content' : 'subject';
                    $query->where($column, 'like', '%' . $keyword . '%');
                }
            )
            ->orderByDesc('idx')
            ->paginate($perPage);
    }

    /**
     * 같은 그룹/카테고리 최신 노트 조회 (현재 노트 제외)
     *
     * @param string $groupCode
     * @param string $categoryCode
     * @param int $excludeIdx
     * @param bool $isAdmin
     * @param int $limit
     * @return Collection<int, Note>
     */
    public function getLatestByCodesExcluding(
        string $groupCode,
        string $categoryCode,
        int $excludeIdx,
        bool $isAdmin,
        int $limit = 5
    ): Collection {
        return Note::with(['topic'])
            ->where('group_code', $groupCode)
            ->where('categories_code', $categoryCode)
            ->where('idx', '!=', $excludeIdx)
            ->when(! $isAdmin, function ($query) {
                $query->where('use_flag', 1);
            })
            ->orderByDesc('idx')
            ->limit($limit)
            ->get();
    }
}
