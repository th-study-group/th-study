<?php

namespace App\Repositories;

use App\Models\Note;

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
     * 노트 상세 조회
     *
     * @param int $idx
     * @param string $groupCode
     * @param string $categoryCode
     * @return Note
     */
    public function findByIdxAndCodes(int $idx, string $groupCode, string $categoryCode): Note
    {
        $note = Note::with(['tags', 'category', 'group'])
            ->where('idx', $idx)
            ->where('group_code', $groupCode)
            ->where('categories_code', $categoryCode)
            ->firstOrFail();

        $groupName = $note->group?->name ?? $note->group_code ?? '-';
        $topicName = $note->category?->name ?? $note->categories_code ?? '-';
        $note->setAttribute('group_topic_name', "{$groupName} > {$topicName}");

        return $note;
    }
}
