<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\AccessLogRequest;
use App\Http\Requests\Mcp\Tools\BotAccessLogRequest;
use App\Http\Requests\Mcp\Tools\ConversionLogRequest;
use App\Http\Requests\Mcp\Tools\DailyPageStatRequest;
use App\Http\Resources\Mcp\Tools\AccessLogResource;
use App\Http\Resources\Mcp\Tools\BotAccessLogResource;
use App\Http\Resources\Mcp\Tools\ConversionLogResource;
use App\Http\Resources\Mcp\Tools\DailyPageStatResource;
use App\Services\Api\TrafficLogService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * 유입 전환 로그 MCP API 컨트롤러
 */
class TrafficLogController extends Controller
{
    public function __construct(
        private readonly TrafficLogService $trafficLogService
    ) {}

    /**
     * 사람 유입 목록 반환
     *
     * @param AccessLogRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/access-logs',
        summary: '사람 유입 조회',
        description: '실제 방문자의 유입 로그를 조건에 따라 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'group_code',
                        type: 'string',
                        example: 'blog'
                    ),
                    new OA\Property(
                        property: 'categories_code',
                        type: 'string',
                        example: 'develop'
                    ),
                    new OA\Property(
                        property: 'topic_code',
                        type: 'string',
                        example: '라라벨'
                    ),
                    new OA\Property(
                        property: 'user_idx',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'device_types',
                        type: 'array',
                        items: new OA\Items(
                            type: 'string',
                            enum: ['desktop', 'mobile', 'tablet']
                        ),
                        example: ['desktop', 'mobile']
                    ),
                    new OA\Property(
                        property: 'ip',
                        type: 'string',
                        example: '192.168.0.1'
                    ),
                    new OA\Property(
                        property: 'referer_host',
                        type: 'string',
                        example: 'www.google.com'
                    ),
                    new OA\Property(
                        property: 'access_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'start_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-01'
                    ),
                    new OA\Property(
                        property: 'end_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'has_note',
                        type: 'boolean',
                        example: true
                    ),
                    new OA\Property(
                        property: 'page',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'per_page',
                        type: 'integer',
                        example: 20
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '사람 유입 조회 성공'
            ),
            new OA\Response(
                response: 401,
                description: '인증 실패'
            ),
            new OA\Response(
                response: 422,
                description: '유효성 검사 실패'
            ),
        ]
    )]
    public function getAccessLogs(AccessLogRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $accessLogs = $this->trafficLogService->getAccessLogs($data);

        return response()->json([
            'success' => true,
            'message' => '사람 유입 목록 조회 성공',
            'data' => AccessLogResource::collection(
                $accessLogs->items()
            ),
            'pagination' => [
                'current_page' => $accessLogs->currentPage(),
                'per_page' => $accessLogs->perPage(),
                'total' => $accessLogs->total(),
                'last_page' => $accessLogs->lastPage(),
                'has_more' => $accessLogs->hasMorePages(),
                'next_page' => $accessLogs->hasMorePages()
                    ? $accessLogs->currentPage() + 1 : null,
            ],
        ]);
    }

    /**
     * 봇 유입 목록 반환
     *
     * @param BotAccessLogRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/bot-access-logs',
        summary: '봇 유입 조회',
        description: '검색엔진 및 AI 크롤러의 유입 로그를 조건에 따라 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'group_code',
                        type: 'string',
                        example: 'blog'
                    ),
                    new OA\Property(
                        property: 'categories_code',
                        type: 'string',
                        example: 'develop'
                    ),
                    new OA\Property(
                        property: 'topic_code',
                        type: 'string',
                        example: '라라벨'
                    ),
                    new OA\Property(
                        property: 'bot_name',
                        type: 'string',
                        example: 'Googlebot'
                    ),
                    new OA\Property(
                        property: 'access_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'start_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-01'
                    ),
                    new OA\Property(
                        property: 'end_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'referer_host',
                        type: 'string',
                        example: 'www.google.com'
                    ),
                    new OA\Property(
                        property: 'has_note',
                        type: 'boolean',
                        example: true
                    ),
                    new OA\Property(
                        property: 'page',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'per_page',
                        type: 'integer',
                        example: 20
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '봇 유입 조회 성공'
            ),
            new OA\Response(
                response: 401,
                description: '인증 실패'
            ),
            new OA\Response(
                response: 422,
                description: '유효성 검사 실패'
            ),
        ]
    )]
    public function getBotAccessLogs(BotAccessLogRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $botAccessLogs = $this->trafficLogService->getBotAccessLogs($data);

        return response()->json([
            'success' => true,
            'message' => '봇 유입 목록 조회 성공',
            'data' => BotAccessLogResource::collection(
                $botAccessLogs->items()
            ),
            'pagination' => [
                'current_page' => $botAccessLogs->currentPage(),
                'per_page' => $botAccessLogs->perPage(),
                'total' => $botAccessLogs->total(),
                'last_page' => $botAccessLogs->lastPage(),
                'has_more' => $botAccessLogs->hasMorePages(),
                'next_page' => $botAccessLogs->hasMorePages()
                    ? $botAccessLogs->currentPage() + 1 : null,
            ],
        ]);
    }

    /**
     * 유입 후 전환 목록 반환
     *
     * @param ConversionLogRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/conversion-logs',
        summary: '유입 후 전환 조회',
        description: '사용자가 유입된 이후 발생한 클릭 및 페이지 이동 등의 전환 로그를 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'group_code',
                        type: 'string',
                        example: 'blog'
                    ),
                    new OA\Property(
                        property: 'categories_code',
                        type: 'string',
                        example: 'develop'
                    ),
                    new OA\Property(
                        property: 'topic_code',
                        type: 'string',
                        example: '라라벨'
                    ),
                    new OA\Property(
                        property: 'user_idx',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'device_types',
                        type: 'array',
                        items: new OA\Items(
                            type: 'string',
                            enum: ['desktop', 'mobile', 'tablet']
                        ),
                        example: ['desktop', 'mobile']
                    ),
                    new OA\Property(
                        property: 'ip',
                        type: 'string',
                        example: '192.168.0.1'
                    ),
                    new OA\Property(
                        property: 'referer_host',
                        type: 'string',
                        example: 'www.google.com'
                    ),
                    new OA\Property(
                        property: 'conversion_type',
                        type: 'string',
                        example: 'outbound'
                    ),
                    new OA\Property(
                        property: 'conversion_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'start_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-01'
                    ),
                    new OA\Property(
                        property: 'end_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'has_note',
                        type: 'boolean',
                        example: true
                    ),
                    new OA\Property(
                        property: 'page',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'per_page',
                        type: 'integer',
                        example: 20
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '유입 후 전환 조회 성공'
            ),
            new OA\Response(
                response: 401,
                description: '인증 실패'
            ),
            new OA\Response(
                response: 422,
                description: '유효성 검사 실패'
            ),
        ]
    )]
    public function getConversionLogs(ConversionLogRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $conversionLogs = $this->trafficLogService->getConversionLogs($data);

        return response()->json([
            'success' => true,
            'message' => '유입 후 전환 목록 조회 성공',
            'data' => ConversionLogResource::collection(
                $conversionLogs->items()
            ),
            'pagination' => [
                'current_page' => $conversionLogs->currentPage(),
                'per_page' => $conversionLogs->perPage(),
                'total' => $conversionLogs->total(),
                'last_page' => $conversionLogs->lastPage(),
                'has_more' => $conversionLogs->hasMorePages(),
                'next_page' => $conversionLogs->hasMorePages()
                    ? $conversionLogs->currentPage() + 1 : null,
            ],
        ]);
    }

    /**
     * 유입/전환 통계 목록 반환
     *
     * @param DailyPageStatRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/daily-page-stat-logs',
        summary: '유입/전환 통계 조회',
        description: '사람 유입 로그와 전환 로그를 기준으로 집계된 일별 통계를 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'group_code',
                        type: 'string',
                        example: 'blog'
                    ),
                    new OA\Property(
                        property: 'categories_code',
                        type: 'string',
                        example: 'develop'
                    ),
                    new OA\Property(
                        property: 'topic_code',
                        type: 'string',
                        example: '라라벨'
                    ),
                    new OA\Property(
                        property: 'device_types',
                        type: 'array',
                        items: new OA\Items(
                            type: 'string',
                            enum: ['desktop', 'mobile', 'tablet']
                        ),
                        example: ['desktop', 'mobile']
                    ),
                    new OA\Property(
                        property: 'stat_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'start_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-01'
                    ),
                    new OA\Property(
                        property: 'end_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-07-27'
                    ),
                    new OA\Property(
                        property: 'has_note',
                        type: 'boolean',
                        example: true
                    ),
                    new OA\Property(
                        property: 'page',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'per_page',
                        type: 'integer',
                        example: 20
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '유입/전환 통계 조회 성공'
            ),
            new OA\Response(
                response: 401,
                description: '인증 실패'
            ),
            new OA\Response(
                response: 422,
                description: '유효성 검사 실패'
            ),
        ]
    )]
    public function getDailyPageStatLogs(DailyPageStatRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $dailyPageStatLogs = $this->trafficLogService->getDailyPageStatLogs($data);

        return response()->json([
            'success' => true,
            'message' => '유입/전환 통계 목록 조회 성공',
            'data' => DailyPageStatResource::collection(
                $dailyPageStatLogs->items()
            ),
            'pagination' => [
                'current_page' => $dailyPageStatLogs->currentPage(),
                'per_page' => $dailyPageStatLogs->perPage(),
                'total' => $dailyPageStatLogs->total(),
                'last_page' => $dailyPageStatLogs->lastPage(),
                'has_more' => $dailyPageStatLogs->hasMorePages(),
                'next_page' => $dailyPageStatLogs->hasMorePages()
                    ? $dailyPageStatLogs->currentPage() + 1 : null,
            ],
        ]);
    }
}
