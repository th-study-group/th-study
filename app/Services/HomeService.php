<?php

namespace App\Services;

use App\Models\Note;
use App\Repositories\HomeRepository;
use App\Services\ContentCacheService;
use Illuminate\Support\Str;

class HomeService
{
    public function __construct(
        private HomeRepository $homeRepository,
        private ContentCacheService $contentCacheService
    ) {}

    public function getLatestBlogs(): array
    {
        return $this->contentCacheService->remember(
            resource: 'blog',
            context: 'home',
            scope: 'public',
            limit: 5,
            ttlMinutes: 60,
            callback: function (int $limit): array {
                return $this->homeRepository
                    ->getLatestBlogs($limit)
                    ->map(function (Note $note): array {
                        $categoryCode = (string) ($note->categories_code ?? '');

                        $plainText = trim((string) preg_replace(
                            '/\s+/u',
                            ' ',
                            html_entity_decode(
                                strip_tags((string) ($note->content ?? '')),
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            )
                        ));

                        return [
                            'category' => (string) config(
                                "note.blogs.{$categoryCode}.title",
                                '-'
                            ),
                            'date' => $note->create_datetime?->format('Y.m.d') ?? '-',
                            'title' => (string) ($note->subject ?? ''),
                            'description' => $plainText !== ''
                                ? Str::limit($plainText, 90)
                                : '작성된 요약이 없습니다.',
                            'show_url' => route(
                                'blogs.show',
                                [
                                    'slug' => $categoryCode,
                                    'idx' => $note->idx,
                                ],
                                false
                            ),
                        ];
                    })
                    ->values()
                    ->all();
            }
        );
    }
}
