<?php

namespace App\Repositories;

use App\Models\Note;
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
    public function paginateByCodes(string $groupCode, string $categoryCode, bool $isAdmin, int $perPage = 20): LengthAwarePaginator
    {
        return Note::with(['category', 'group', 'topic', 'tags'])
            ->where('group_code', $groupCode)
            ->where('categories_code', $categoryCode)
            ->when(! $isAdmin, function ($query) {
                $query->where('use_flag', 1);
            })
            ->orderByDesc('idx')
            ->paginate($perPage);
    }
}
