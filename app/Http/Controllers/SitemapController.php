<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;

/**
 * 사이트맵 컨트롤러
 */
class SitemapController extends Controller
{
    public function __construct(
        private SitemapService $sitemapService
    ) {}

    /**
     * 사이트맵 조회
     */
    public function index()
    {
        $xml = $this->sitemapService->getSitemapXml();

        return response(
            $xml,
            200,
            [
                'Content-Type' => 'application/xml; charset=UTF-8',
            ]
        );
    }
}
