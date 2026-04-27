<?php

namespace App\Repositories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

/**
 * 홈 페이지 관련 데이터를 처리하는 리포지토리입니다.
 */
class HomeRepository
{
    /**
     *  최신 블로그 글을 가져옵니다.
     *
     * @param integer $limit
     * @return Collection
     */
    public function getLatestBlogs(int $limit = 5): Collection
    {
        return Note::with(['category'])
            ->where('group_code', 'blog')
            ->where('use_flag', 1)
            ->orderByDesc('create_datetime')
            ->orderByDesc('idx')
            ->limit($limit)
            ->get();
    }
}
