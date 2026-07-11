<?php

namespace App\Services\Api;

use App\Repositories\Api\NoteGroupRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 노트 그룹 API 서비스
 */
class NoteGroupService
{
    public function __construct(
        private readonly NoteGroupRepository $noteGroupRepository
    ) {}

    /**
     * 노트 그룹 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getNoteGroups(array $data): LengthAwarePaginator
    {
        $noteGroups = $this->noteGroupRepository->paginateNoteGroups($data);

        Log::info('[NoteGroup][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $noteGroups->currentPage(),
                'per_page' => $noteGroups->perPage(),
                'total' => $noteGroups->total(),
                'last_page' => $noteGroups->lastPage(),
            ],
            'count' => $noteGroups->count(),
        ]);

        return $noteGroups;
    }
}