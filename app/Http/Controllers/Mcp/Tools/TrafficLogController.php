<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\AccessLogRequest;
use App\Http\Requests\Mcp\Tools\BotAccessLogRequest;
use App\Http\Resources\Mcp\Tools\AccessLogResource;
use App\Http\Resources\Mcp\Tools\BotAccessLogResource;
use App\Services\Api\TrafficLogService;
use Illuminate\Http\JsonResponse;

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
}
