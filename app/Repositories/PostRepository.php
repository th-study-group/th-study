<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * 게시글 레퍼지토리
 */
class PostRepository
{
    /**
     * 추가
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        $post = new Post();
        $post->forceFill($data);
        $post->save();

        return $post;
    }


    /**
     * 수정
     *
     * @param Post $post
     * @param array $data
     * @return Post
     */
    public function update(Post $post, array $data): Post
    {
        $post->forceFill($data);
        $post->save();

        return $post;
    }

    /**
     * 게시글 조회
     *
     * @param integer $idx
     * @param string $postType
     * @return Post
     */
    public function findByIdxAndType(string $idx, string $postType): Post
    {
        return Post::with('user')
            ->where('idx', $idx)
            ->where('post_type', $postType)
            ->firstOrFail();
    }

    /**
     * 사용자 문의내역 목록 (페이징)
     *
     * @param integer $userIdx
     * @param string $postType
     * @param array $filters
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByUserAndType(int $userIdx, string $postType, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $startDate = $filters['search_start_date'] ?? null;
        $endDate = $filters['search_end_date'] ?? null;
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        return Post::where('user_idx', $userIdx)
            ->where('post_type', $postType)
            ->when($start, function ($q) use ($start) {
                $q->where('create_datetime', '>=', $start);
            })
            ->when($end, function ($q) use ($end) {
                $q->where('create_datetime', '<=', $end);
            })
            ->orderByDesc('idx')
            ->paginate($perPage);
    }

    /**
     * 관리자 문의내역 목록 (페이징)
     *
     * @param string $postType
     * @param array $filters
     * @param integer $perPage
     * @return LengthAwarePaginator
     */
    public function paginateByType(string $postType, array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $startDate = $filters['search_start_date'] ?? null;
        $endDate = $filters['search_end_date'] ?? null;
        $status = $filters['search_status'] ?? null;
        $searchName = $filters['search_name'] ?? null;
        $searchTitle = $filters['search_title'] ?? ($filters['search_subject'] ?? null);
        $useFlag = array_key_exists('use_flag', $filters) ? $filters['use_flag'] : null;
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        return Post::with('user')
            ->where('post_type', $postType)
            ->when($start, function ($q) use ($start) {
                $q->where('create_datetime', '>=', $start);
            })
            ->when($end, function ($q) use ($end) {
                $q->where('create_datetime', '<=', $end);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($searchName, function ($q) use ($searchName) {
                $q->whereHas('user', function ($subQuery) use ($searchName) {
                    $subQuery->where('name', 'like', '%' . $searchName . '%')
                        ->orWhere('nick_name', 'like', '%' . $searchName . '%');
                });
            })
            ->when($searchTitle, function ($q) use ($searchTitle) {
                $q->where('title', 'like', '%' . $searchTitle . '%');
            })
            ->when($useFlag !== null, function ($q) use ($useFlag) {
                $q->where('use_flag', $useFlag);
            })
            ->orderByDesc('idx')
            ->paginate($perPage);
    }

}
