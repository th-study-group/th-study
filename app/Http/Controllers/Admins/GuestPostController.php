<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuestPosts\Admin\GuestPostSearchRequest;
use App\Http\Requests\GuestPosts\Admin\GuestPostUpdateRequest;
use App\Services\GuestPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * 미인증 고객 게시판 컨트롤러 
 */
class GuestPostController extends Controller
{
    public function __construct(
        private GuestPostService $guestPostService
    ) {}

    /**
     * 글목록
     *
     * @return void
     */
    public function index(GuestPostSearchRequest $request, string $postType): View
    {
        $filters = $request->validated();
        $posts = $this->guestPostService->getGuestPosts($postType, $filters);
        $posts->appends($filters);

        return view('admins.guest_posts.index', [
            'posts' => $posts,
            'filters' => $filters,
            'postType' => $postType,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 수정화면
     *
     * @param string $postType
     * @param string $idx
     * @return void
     */
    public function edit(string $postType, string $idx): View
    {
        $post = $this->guestPostService->getByIdx($idx, $postType);

        return view('admins.guest_posts.edit', [
            'post' => $post,
            'postType' => $postType,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 수정 처리
     *
     * @param Request $request
     * @return void
     */
    public function update(GuestPostUpdateRequest $request, string $postType, string $idx)
    {
        $post = $this->guestPostService->getByIdx($idx, $postType);
        $validated = $request->validated();

        $this->guestPostService->update($post, $validated);

        return to_route('admins.guest_posts.edit', [
            'post_type' => $postType,
            'idx' => $idx,
        ]);
    }

    /**
     * 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $postType, string $idx): JsonResponse
    {
        $post = $this->guestPostService->getByIdx($idx, $postType);
        $this->guestPostService->delete($post);

        return response()->json([
            'message' => '삭제되었습니다.',
        ]);
    }
}
