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

        return view('admins.traffics.index', [
            'logs' => $logs,
            'filters' => $filters,
            'deviceOptions' => config('const.device_kind', []),
        ]);
    }
}
