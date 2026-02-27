<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

/**
 * 노트 권한 정책
 */
class NotePolicy
{
    /**
     * 노트 목록 조회 권한
     */
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    /**
     * 노트 상세 조회 권한
     */
    public function view(?User $user = null, ?Note $note = null): bool
    {
        return true;
    }

    /**
     * 노트 작성 권한
     */
    public function create(User $user): bool
    {
        return $user->level === 'admin';
    }

    /**
     * 노트 수정 권한
     */
    public function update(User $user, Note $note): bool
    {
        return $user->level === 'admin';
    }

    /**
     * 노트 삭제 권한
     */
    public function delete(User $user, Note $note): bool
    {
        return $user->level === 'admin' && $note->use_flag !== 'Y';
    }

    /**
     * 노트 공개여부 수정 권한
     */
    public function updateUseFlag(User $user, Note $note): bool
    {
        return $user->level === 'admin';
    }
}
