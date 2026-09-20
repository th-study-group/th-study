<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\PostSearchRequest;
use App\Services\PostService;
use App\Support\RequestIp;
use Illuminate\Http\Request;

/**
 * 사용자 게시판 컨트롤러
 */
class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    /**
     * 글 목록
     *
     * @return void
     */
    public function index(PostSearchRequest $request, string $postType)
    {
        $filters = $request->validated();
        $posts = $this->postService->getPublicPosts($postType, $filters);
        $posts->appends($filters);

        return view("{$postType}.index", [
            'posts' => $posts,
            'filters' => $filters,
        ]);
    }

    /**
     * 상세내역
     *
     * @return void
     */
    public function show(string $postType, string $idx)
    {
        $post = $this->postService->getPublicByIdxWithHistory(
            $idx,
            $postType,
            RequestIp::resolve(),
            request()->userAgent()
        );

        return view("{$postType}.show", [
            'post' => $post,
            'adsenseNoticeShowTopDisplayAdSlot' => config('adsense.units.common_top_display.ad_slot'),
            'adsenseNoticeShowTopDisplayFormat' => config('adsense.units.common_top_display.format'),
            'adsenseNoticeShowTopDisplayFullWidthResponsive' => config('adsense.units.common_top_display.full_width_responsive'),
            'adsenseNoticeShowContentInArticleAdSlot' => config('adsense.units.common_content_in_article.ad_slot'),
            'adsenseNoticeShowContentInArticleFormat' => config('adsense.units.common_content_in_article.format'),
            'adsenseNoticeShowContentInArticleLayout' => config('adsense.units.common_content_in_article.layout'),
            'adsenseNoticeShowBottomMultiplexAdSlot' => config('adsense.units.common_bottom_multiplex.ad_slot'),
            'adsenseNoticeShowBottomMultiplexFormat' => config('adsense.units.common_bottom_multiplex.format'),
        ]);
    }

    /**
     * 등록 폼
     *
     * @return void
     */
    public function create(string $postType)
    {
        return view("{$postType}.create");
    }

    /**
     * 등록 처리 
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request, string $postType)
    {
    }

    /**
     * 수정 폼
     *
     * @param string $idx
     * @return void
     */
    public function edit(string $postType, string $idx)
    {
        return view("{$postType}.create");
    }

    /**
     * 수정 처리 
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * 삭제 (soft delete)
     *
     * @param string $idx
     * @return void
     */
    public function destroy(string $idx)
    {
    }
}
