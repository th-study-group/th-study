<?php

namespace App\Services\Api;

use App\Repositories\Api\NoteRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 블로그 MCP 서비스
 */
class NoteService
{
    public function __construct(
        private readonly NoteRepository $noteRepository
    ) {}

    /* 블로그 글 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getBlogs(array $data) : LengthAwarePaginator
    {
        $notes = $this->noteRepository->paginateNotes($data);

        Log::info('[Note][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
                'last_page' => $notes->lastPage(),
            ],
            'count' => $notes->count(),
        ]);

        return $notes;
    }
}