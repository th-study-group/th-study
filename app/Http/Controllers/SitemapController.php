<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\Config;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * 사이트맵 컨트롤러
 */
class SitemapController extends Controller
{
    /**
     * 사이트맵 등록
     *
     * @return void
     */
    public function index()
    {
        $items = Config::get('sitemap.urls', []);
        $sitemap = Sitemap::create();

        foreach ($items as $item) {
            $url = Url::create($item['loc']);

            if (!empty($item['changefreq'])) {
                $url->setChangeFrequency($item['changefreq']);
            }
            if (!empty($item['priority'])) {
                $url->setPriority($item['priority']);
            }
            if (!empty($item['lastmod'])) {
                $url->setLastModificationDate(new DateTime($item['lastmod']));
            }

            $sitemap->add($url);
        }

        return $sitemap->toResponse(request());
    }
}
