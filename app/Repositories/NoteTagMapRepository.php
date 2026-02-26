<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

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

        DB::table('note_tag_map')->insertOrIgnore($rows);
    }
}
