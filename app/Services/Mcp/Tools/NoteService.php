<?php

namespace App\Services\Mcp\Tools;

use App\Repositories\Mcp\Tools\NoteRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * 블로그 MCP API 서비스
 */
class NoteService
{
    public function __construct(
        private readonly NoteRepository $noteRepository
    ) {}

    /* 블로그 글 목록 반환
     *
     * @param array $data
     * @return void
     */
    public function getBlogs(array $data) : LengthAwarePaginator
    {
        return $this->noteRepository->paginateNotes($data);
    }

}