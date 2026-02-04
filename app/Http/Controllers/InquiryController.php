<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inquiries\StoreInquiryRequest;
use App\Http\Requests\Inquiries\UpdateInquiryRequest;
use App\Http\Requests\Inquiries\InquirySearchRequest;
use App\Services\CommentService;
use App\Services\InquiryService;

/**
 * 문의내역
 */
class InquiryController extends Controller
{
    public function __construct(
        private InquiryService $inquiryService,
        private CommentService $commentService
    ) {}
    /**
     * 글 목록
     *
     * @return void
     */
    public function index(InquirySearchRequest $request) 
    {
        $filters = $request->validated();
        $posts = $this->inquiryService->getMyInquiries($filters);
        $posts->appends($filters);

        return view('inquiries.index', [
            'posts' => $posts,
            'filters' => $filters,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 글 작성 화면
     *
     * @return void
     */
    public function create()
    {
        return view('inquiries.create');
    }

    /**
     * 글 신규 등록
     *
     * @param Request $reuqest
     * @return void
     */
    public function store(StoreInquiryRequest $request)
    {
        $post = $this->inquiryService->create($request, 'inquiries');

        return to_route('inquiries.show', [
            'idx' => $post->idx
        ]);
    }

    /**
     * 글 상세 화면
     *
     * @return void
     */
    public function show(string $idx)
    {
        $post = $this->inquiryService->getByIdxWithHistory(
            $idx,
            'inquiries',
            request()->ip(),
            request()->userAgent()
        );
        $this->authorize('view', $post);
        $comments = $this->commentService->getByPostIdx($post->idx);

        return view('inquiries.show', [
            'post' => $post,
            'comments' => $comments,
            'statusList' => config('board.status'),
            'statusBadgeClasses' => config('board.status_badge_classes'),
        ]);
    }

    /**
     * 글 수정 화면
     *
     * @return void
     */
    public function edit(string $idx)
    {
        $post = $this->inquiryService->getByIdx($idx, 'inquiries');
        $this->authorize('update', $post);

        return view('inquiries.create', [
            'post' => $post,
            'mode' => 'edit',
        ]);
    }

    /**
     * 글 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(UpdateInquiryRequest $request, string $idx)
    {
        $post = $this->inquiryService->getByIdx($idx, 'inquiries');
        $this->authorize('update', $post);

        $payload = $request->safe()->only(['title', 'content']);
        $payload['ip'] = $request->ip();
        $payload['user_agent'] = $request->userAgent();
        $this->inquiryService->update($payload, $post);

        return to_route('inquiries.show', ['idx' => $post->idx]);
    }

    /**
     * 글 삭제 (soft delete)
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
