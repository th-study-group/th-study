<?php
return [
    'urls' => [
        // 메인
        [
            'loc'        => config('app.url'),
            'changefreq' => 'daily',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 소개
        [
            'loc'        => config('app.url') . '/intro',
            'changefreq' => 'daily',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 공지사항 목록
        [
            'loc'        => config('app.url') . '/posts/notice',
            'changefreq' => 'weekly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 블로그 목록 (전체)
        [
            'loc'        => config('app.url') . '/blogs',
            'changefreq' => 'weekly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 블로그 목록 (개발)
        [
            'loc'        => config('app.url') . '/blogs/develop',
            'changefreq' => 'weekly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 블로그 목록 (일상)
        [
            'loc'        => config('app.url') . '/blogs/life',
            'changefreq' => 'weekly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 블로그 목록 (경제)
        [
            'loc'        => config('app.url') . '/blogs/economy',
            'changefreq' => 'weekly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
        // 포트폴리오
        [
            'loc'        => config('app.url') . '/portfolio',
            'changefreq' => 'monthly',
            'priority'   => 1.0,
            'lastmod'    => '2026-03-01',
        ],
    ]
];