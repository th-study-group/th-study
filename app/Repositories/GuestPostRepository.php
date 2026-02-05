<?php

namespace App\Repositories;

use App\Models\GuestPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * 미인증 게시글 레퍼지토리
 */
class GuestPostRepository
{

    /**
     * 특정 게시글 조회 
     *
     * @param string $idx
     * @param string $postType
     * @return GuestPost
     */
    public function findByIdxAndType(string $idx, string $postType): GuestPost
    {
        return GuestPost::where('idx', $idx)
            ->where('post_type', $postType)
            ->firstOrFail();
    }

    /**
     * 게시글 목록 
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
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        return GuestPost::where('post_type', $postType)
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
                $q->where('writer', 'like', '%' . $searchName . '%');
            })
            ->orderByDesc('idx')
            ->paginate($perPage);
    }

    /**
     * 게시글 수정
     *
     * @param GuestPost $post
     * @param array $data
     * @return GuestPost
     */
    public function update(GuestPost $post, array $data): GuestPost
    {
        $post->forceFill($data);
        $post->save();

        return $post;
    }
}
