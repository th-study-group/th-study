<?php

namespace App\Services\Api;

use App\Repositories\Api\NoteTagRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 노트 태그 MCP API 서비스
 */
class NoteTagService
{
    public function __construct(
        private readonly NoteTagRepository $noteTagRepository
    ) {}

    /**
     * 노트 태그 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getNoteTags(array $data) : LengthAwarePaginator
    {
        $noteTags = $this->noteTagRepository->paginateNoteTags($data);

        Log::info('[NoteTag][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $noteTags->currentPage(),
                'per_page' => $noteTags->perPage(),
                'total' => $noteTags->total(),
                'last_page' => $noteTags->lastPage(),
            ],
            'count' => $noteTags->count(),
        ]);


        return $noteTags;
    }
}