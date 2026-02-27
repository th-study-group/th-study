<?php

namespace App\Repositories;

use App\Models\NoteTag;
use App\Models\NoteTagMap;

/**
 * 노트 태그 매핑 레퍼지토리
 */
class NoteTagMapRepository
{
    /**
     * 노트-태그 매핑 저장
     *
     * @param int $noteIdx
     * @param array<int, int> $tagIdxList
     * @return void
     */
    public function insertMappings(int $noteIdx, array $tagIdxList): void
    {
        if (empty($tagIdxList)) {
            return;
        }

        $rows = array_map(function (int $tagIdx) use ($noteIdx): array {
            return [
                'note_idx' => $noteIdx,
                'tag_idx' => $tagIdx,
            ];
        }, $tagIdxList);

        NoteTagMap::query()->insertOrIgnore($rows);
    }

    /**
     * 노트 태그 매핑 교체
     *
     * @param int $noteIdx
     * @param array<int, int> $tagIdxList
     * @return void
     */
    public function replaceMappings(int $noteIdx, array $tagIdxList): void
    {
        NoteTagMap::query()
            ->where('note_idx', $noteIdx)
            ->delete();

        $this->insertMappings($noteIdx, $tagIdxList);
    }

    /**
     * 노트에서 태그명 기준 매핑 삭제
     *
     * @param int $noteIdx
     * @param string $tagName
     * @return int
     */
    public function deleteMappingByTagName(int $noteIdx, string $tagName): int
    {
        $tagIdx = NoteTag::query()
            ->where('name', trim($tagName))
            ->value('idx');

        if ($tagIdx === null) {
            return 0;
        }

        return NoteTagMap::query()
            ->where('note_idx', $noteIdx)
            ->where('tag_idx', (int) $tagIdx)
            ->delete();
    }

    /**
     * 노트에 연결된 태그 idx 목록 조회
     *
     * @param int $noteIdx
     * @return array<int, int>
     */
    public function getTagIdxListByNote(int $noteIdx): array
    {
        return NoteTagMap::query()
            ->where('note_idx', $noteIdx)
            ->pluck('tag_idx')
            ->map(static function ($value): int {
                return (int) $value;
            })
            ->values()
            ->all();
    }

    /**
     * 노트의 태그 매핑 전체 삭제
     *
     * @param int $noteIdx
     * @return int
     */
    public function deleteMappingsByNote(int $noteIdx): int
    {
        return NoteTagMap::query()
            ->where('note_idx', $noteIdx)
            ->delete();
    }

    /**
     * 노트 + 태그명으로 태그 idx 조회
     *
     * @param int $noteIdx
     * @param string $tagName
     * @return int|null
     */
    public function findTagIdxByNoteAndTagName(int $noteIdx, string $tagName): ?int
    {
        $tagIdx = NoteTag::query()
            ->where('name', trim($tagName))
            ->value('idx');

        if ($tagIdx === null) {
            return null;
        }

        $exists = NoteTagMap::query()
            ->where('note_idx', $noteIdx)
            ->where('tag_idx', (int) $tagIdx)
            ->exists();

        return $exists ? (int) $tagIdx : null;
    }

    /**
     * 태그의 매핑 개수
     *
     * @param int $tagIdx
     * @return int
     */
    public function countMappingsByTagIdx(int $tagIdx): int
    {
        return (int) NoteTagMap::query()
            ->where('tag_idx', $tagIdx)
            ->count();
    }
}
