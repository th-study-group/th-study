<?php

namespace App\Repositories;

use App\Models\NoteTag;

/**
 * 노트 태그 레퍼지토리
 */
class NoteTagRepository
{
    /**
     * 태그명으로 태그 조회/생성
     *
     * @param string $name
     * @param int $userIdx
     * @return NoteTag
     */
    public function findOrCreateByName(string $name, int $userIdx): NoteTag
    {
        $name = trim($name);

        $tag = NoteTag::withTrashed()
            ->where('name', $name)
            ->first();

        if ($tag) {
            if ($tag->trashed()) {
                $tag->restore();
            }

            return $tag;
        }

        $tag = new NoteTag();
        $tag->forceFill([
            'name' => $name,
            'create_user_idx' => $userIdx,
        ]);
        $tag->save();

        return $tag;
    }
}
