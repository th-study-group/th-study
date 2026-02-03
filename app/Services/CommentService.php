<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use Illuminate\Support\Collection;

/**
 * 댓글 서비스
 */
class CommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {}

    /**
     * 게시글 댓글 목록
     *
     * @param integer $postIdx
     * @return Collection
     */
    public function getByPostIdx(int $postIdx): Collection
    {
        return $this->commentRepository->getByPostIdx($postIdx);
    }
}
