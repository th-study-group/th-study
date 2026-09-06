<?php

namespace App\Services;

use App\Repositories\NoteRepository;
use DateTime;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    public function __construct(
        private NoteRepository $noteRepository,
        private ContentCacheService $contentCacheService
    ) {}

    /**
     * 사이트맵 XML 조회
     */
    public function getSitemapXml(): string
    {
        return $this->contentCacheService->remember(
            resource: 'blog',
            context: 'sitemap',
            scope: 'public',
            limit: null,
            ttlMinutes: 1440, // 24 hours
            callback: function () {
                return $this->generateSitemapXml();
            }
        );
    }

    /**
     * 사이트맵 XML 생성
     */
    private function generateSitemapXml(): string
    {
        $sitemap = Sitemap::create();

        $this->addStaticUrls($sitemap);
        $this->addBlogUrls($sitemap);

        return $sitemap->render();
    }

    /**
     * 정적 URL 추가
     */
    private function addStaticUrls(Sitemap $sitemap): void
    {
        $items = config('sitemap.urls', []);

        foreach ($items as $item) {
            $url = Url::create($item['loc']);

            if (! empty($item['changefreq'])) {
                $url->setChangeFrequency($item['changefreq']);
            }

            if (! empty($item['priority'])) {
                $url->setPriority($item['priority']);
            }

            $sitemap->add($url);
        }
    }

    /**
     * 공개 블로그 상세 URL 추가
     */
    private function addBlogUrls(Sitemap $sitemap): void
    {
        $blogs = $this->noteRepository
            ->getSitemapBlogs()
            ->map(function ($note) {
                return [
                    'loc' => config('app.url')
                        . '/blogs/'
                        . $note->categories_code
                        . '/'
                        . $note->idx
                        . '/show',

                    'lastmod' => $note->update_datetime
                        ?? $note->create_datetime,
                ];
            });

        foreach ($blogs as $blog) {
            $url = Url::create($blog['loc'])
                ->setChangeFrequency('monthly')
                ->setPriority(0.8);

            if (! empty($blog['lastmod'])) {
                $url->setLastModificationDate(
                    new DateTime((string) $blog['lastmod'])
                );
            }

            $sitemap->add($url);
        }
    }
}