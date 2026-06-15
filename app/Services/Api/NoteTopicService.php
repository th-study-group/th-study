<?php

namespace App\Services\Api;

use App\Repositories\Api\NoteTopicRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 노트 주제 MCP API 서비스
 */
class NoteTopicService
{
    public function __construct(
        private readonly NoteTopicRepository $noteTopicRepository
    ) {}

    /* 노트 주제 목록 반환
     *
     * @param array $data
     * @return Collection
     */
    public function getNoteTopics(array $data) : LengthAwarePaginator
    {
        $noteTopics = $this->noteTopicRepository->paginateNoteTopics($data);

        Log::info('[NoteTopic][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $noteTopics->currentPage(),
                'per_page' => $noteTopics->perPage(),
                'total' => $noteTopics->total(),
                'last_page' => $noteTopics->lastPage(),
            ],
            'count' => $noteTopics->count(),
        ]);

        return $noteTopics;
    }
}