<?php

namespace App\Repositories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

class HomeRepository
{
    /**
     * @return Collection<int, Note>
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
