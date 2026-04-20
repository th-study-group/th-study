<?php

namespace App\Services;

use App\Models\Note;
use App\Repositories\HomeRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomeService
{
    public const LATEST_BLOGS_CACHE_KEY = 'home:latest_blogs:5';
    private const LATEST_BLOGS_LIMIT = 5;
    private const LATEST_BLOGS_CACHE_MINUTES = 60;

    public function __construct(
        private HomeRepository $homeRepository
    ) {}

    public function getHomeData(): array
    {
        return [
            'postType' => 'inquiries',
            'latestBlogs' => $this->getLatestBlogs(),
        ];
    }

    public function getLatestBlogs(): array
    {
        return Cache::remember(
            self::LATEST_BLOGS_CACHE_KEY,
            now()->addMinutes(self::LATEST_BLOGS_CACHE_MINUTES),
            function (): array {
                return $this->homeRepository
                    ->getLatestBlogs(self::LATEST_BLOGS_LIMIT)
                    ->map(function (Note $note): array {
                        $categoryCode = (string) ($note->categories_code ?? '');
                        $plainText = trim((string) preg_replace(
                            '/\s+/u',
                            ' ',
                            html_entity_decode(strip_tags((string) ($note->content ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                        ));

                        return [
                            'category' => (string) config("note.blogs.{$categoryCode}.title", '-'),
                            'date' => $note->create_datetime?->format('Y.m.d') ?? '-',
                            'title' => (string) ($note->subject ?? ''),
                            'description' => $plainText !== '' ? Str::limit($plainText, 90) : '작성된 요약이 없습니다.',
                            'show_url' => route('blogs.show', [
                                'slug' => $categoryCode,
                                'idx' => $note->idx,
                            ], false),
                        ];
                    })
                    ->values()
                    ->all();
            }
        );
    }
}
