<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\TrafficSearchRequest;
use App\Services\TrafficAnalyticsService;
use Illuminate\View\View;

/**
 * 유입 컨트롤러
 */
class TrafficController extends Controller
{
    public function __construct(
        private TrafficAnalyticsService $trafficAnalyticsService
    ) {}

    /**
     * 일일 유입 현황
     *
     * @return void
     */
    public function index(TrafficSearchRequest $request): View
    {
        $filters = $request->validated();
        $logs = $this->trafficAnalyticsService->getDailyAccessLogs($filters);
        $logs->appends($filters);
        $deviceOptions = config('const.device_kind', []);
        $deviceLabelMap = [
            'desktop' => $deviceOptions['pc'] ?? 'PC',
            'mobile' => $deviceOptions['mobile'] ?? '모바일',
            'tablet' => $deviceOptions['tablet'] ?? '태블릿',
        ];
        $baseUrl = rtrim((string) config('app.url', ''), '/');

        $logs->setCollection(
            $logs->getCollection()->map(function ($log) use ($deviceLabelMap, $baseUrl) {
                $accessPage = trim((string) ($log->access_page ?? ''));

                $log->device_label = $deviceLabelMap[$log->device_type] ?? ($log->device_type ?? '-');
                $log->access_page_href = $accessPage !== ''
                    ? $baseUrl . '/' . ltrim($accessPage, '/')
                    : null;

                return $log;
            })
        );

        return view('admins.traffics.index', [
            'logs' => $logs,
            'filters' => $filters,
            'deviceOptions' => $deviceOptions,
        ]);
    }
}
