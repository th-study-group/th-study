<?php

namespace App\Http\Controllers;

use App\Http\Requests\Traffics\ConversionLogRequest;
use App\Services\TrafficAnalyticsService;

/**
 * 유입에 따른 전환 처리하는 컨트롤러
 */
class TrafficController extends Controller
{
    /**
     * 유입글에서 다른 페이지로 이동 시 전환 로그 기록
     *
     * @param ConversionLogRequest $request
     * @param TrafficAnalyticsService $trafficAnalyticsService
     * @return void
     */
    public function outbound(
        ConversionLogRequest $request,
        TrafficAnalyticsService $trafficAnalyticsService
    ) {
        $payload = $request->validated();

        $trafficAnalyticsService->trackConversion(
            $request,
            $payload['conversion_type'],
            $payload['url'],
            $payload['source_page'] ?? null
        );

        return redirect()->away($payload['url'], 302)
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * 공유하기 버튼 클릭 시 전환 로그 기록
     *
     * @param ConversionLogRequest $request
     * @param TrafficAnalyticsService $trafficAnalyticsService
     * @return void
     */
    public function share(
        ConversionLogRequest $request,
        TrafficAnalyticsService $trafficAnalyticsService
    ) {
        $payload = $request->validated();

        $trafficAnalyticsService->trackConversion(
            $request,
            $payload['conversion_type'],
            $payload['url'],
            $payload['source_page'] ?? null
        );

        return response()->json([
            'message' => '공유 전환이 저장되었습니다.',
        ]);
    }
}
