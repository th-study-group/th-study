<?php

namespace App\Http\Controllers\Admins;

use App\Http\Requests\Inquiries\Admin\InquirySearchRequest;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\InquiryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 문의내역 관리  
 */
class InquiryController extends Controller
{
    public function __construct(
        private InquiryService $inquiryService,
        private CommentService $commentService
    ) {}

    /**
     * 문의내역 목록
     *
     * @return void
     */
    public function index(InquirySearchRequest $request) : View
    {
        $this->authorize('viewAny', Post::class);

        $filters = $request->validated();
        $posts = $this->inquiryService->getInquiries($filters);
        $posts->appends($filters);

        return view('admins.inquiries.index',[
            'posts' => $posts,
            'filters' => $filters,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 문의 상세 내역
     *
     * @return void
     */
    public function show(string $idx)
    {
        $this->authorize('viewAny', Post::class);
        $post = $this->inquiryService->getByIdxWithHistory(
            $idx,
            'inquiries',
            request()->ip(),
            request()->userAgent()
        );
        $comments = $this->commentService->getByPostIdx($post->idx);

        return view('admins.inquiries.show', [
            'post' => $post,
            'comments' => $comments,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 문의 내역 상태 변경 
     */
    public function updateStatus(Request $request, string $idx)
    {
        $post = $this->inquiryService->getByIdx($idx, 'inquiries');
        $this->authorize('updateStatus', $post);

        $statusKeys = array_keys(config('board.status', []));
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $statusKeys)],
        ]);

        $updatedPost = $this->inquiryService->updateStatus($post, [
            'status' => $validated['status'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => $updatedPost->status,
            'status_label' => config('board.status')[$updatedPost->status] ?? $updatedPost->status,
            'status_badge_class' => config('board.status_badge_classes')[$updatedPost->status] ?? 'secondary',
        ]);
    }

    /**
     * 문의내역 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
        $post = $this->inquiryService->getByIdx($idx, 'inquiries');
        $this->authorize('delete', $post);

        $payload = [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        $this->inquiryService->delete($post, $payload);

        return response()->json([
            'message' => '삭제되었습니다.',
        ]);
    }
}
