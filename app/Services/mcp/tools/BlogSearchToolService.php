<?php

namespace App\Services\Mcp\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BlogSearchToolService
{
    public function search(array $args): array
    {
        $startedAt = microtime(true);

        $title = $args['title'] ?? null;
        $status = $args['status'] ?? null;
        $createdAt = $args['created_at'] ?? null;
        $from = $args['created_at_from'] ?? null;
        $to = $args['created_at_to'] ?? null;
        $limit = min((int) ($args['limit'] ?? 20), 100);

        Log::channel('mcp')->info('MCP blog_search started', [
            'title_exists' => !empty($title),
            'status_exists' => !empty($status),
            'created_at_exists' => !empty($createdAt),
            'from_exists' => !empty($from),
            'to_exists' => !empty($to),
            'limit' => $limit,
        ]);

        if ($from && $to) {
            try {
                $fromDate = Carbon::parse($from);
                $toDate = Carbon::parse($to);

                if ($fromDate->diffInDays($toDate) > 365) {
                    Log::channel('mcp')->warning('MCP blog_search long range detected', [
                        'created_at_from' => $from,
                        'created_at_to' => $to,
                    ]);

                    return [
                        'count' => 0,
                        'split_required' => true,
                        'message' => '1년 이상 장기간 조회 요청입니다. 월별 또는 분기별로 나누어 조회하는 것을 권장합니다.',
                        'suggestion' => [
                            'created_at_from' => $from,
                            'created_at_to' => $to,
                            'split_by' => 'month',
                        ],
                    ];
                }
            } catch (\Throwable $e) {
                Log::channel('mcp')->warning('MCP blog_search invalid date range', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $rows = collect([
            [
                'id' => 1,
                'title' => '라라벨 ORM 정리',
                'view_count' => 120,
                'status' => 'published',
                'created_at' => '2026-05-19',
            ],
            [
                'id' => 2,
                'title' => 'FastAPI JWT 로그인 구현',
                'view_count' => 85,
                'status' => 'published',
                'created_at' => '2026-05-18',
            ],
            [
                'id' => 3,
                'title' => '라라벨 MCP OAuth 연동',
                'view_count' => 45,
                'status' => 'draft',
                'created_at' => '2026-05-17',
            ],
            [
                'id' => 4,
                'title' => '티에이치스터디 SEO 개선 기록',
                'view_count' => 300,
                'status' => 'published',
                'created_at' => '2025-12-10',
            ],
        ]);

        if (!empty($title)) {
            $rows = $rows->filter(function ($row) use ($title) {
                return mb_stripos($row['title'], $title) !== false;
            });
        }

        if (!empty($status)) {
            $rows = $rows->filter(function ($row) use ($status) {
                return $row['status'] === $status;
            });
        }

        if (!empty($createdAt)) {
            $rows = $rows->filter(function ($row) use ($createdAt) {
                return $row['created_at'] === $createdAt;
            });
        }

        if (!empty($from)) {
            $rows = $rows->filter(function ($row) use ($from) {
                return $row['created_at'] >= $from;
            });
        }

        if (!empty($to)) {
            $rows = $rows->filter(function ($row) use ($to) {
                return $row['created_at'] <= $to;
            });
        }

        $rows = $rows
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();

        Log::channel('mcp')->info('MCP blog_search completed', [
            'count' => $rows->count(),
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
        ]);

        return [
            'count' => $rows->count(),
            'data' => $rows,
        ];
    }
}