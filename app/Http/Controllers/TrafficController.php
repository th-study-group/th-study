<?php

namespace App\Http\Controllers;

use App\Http\Requests\Traffics\ConversionLogRequest;
use App\Services\TrafficAnalyticsService;

class TrafficController extends Controller
{
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
}
