<?php

namespace App\Http\Controllers;

use App\Http\Requests\Push\ExistsPushRequest;
use App\Http\Requests\Push\PingPushRequest;
use App\Http\Requests\Push\SendPushToUserRequest;
use App\Http\Requests\Push\SubscribePushRequest;
use App\Http\Requests\Push\UnsubscribePushRequest;
use App\Services\PushService;
use Illuminate\Http\Request;

/**
 * 웹앱 푸시 컨트롤러 
 */
class PushController extends Controller
{
    public function __construct(
        private PushService $pushService
    ) {}

    /**
     * 웹 푸시 구독 등록
     *
     * @param SubscribePushRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(SubscribePushRequest $request)
    {
        $data = $request->validated();

        $this->pushService->subscribe(
            $data,
            auth()->id(),
            $request->userAgent()
        );

        return response()->json(['ok' => true]);
    }

    /**
     * 웹 푸시 구독 해제
     *
     * @param UnsubscribePushRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(UnsubscribePushRequest $request)
    {
        $data = $request->validated();

        $this->pushService->unsubscribe($data['endpoint'], auth()->id());

        return response()->json(['ok' => true]);
    }

    /**
     * 웹 푸시 최근 사용 시각 갱신
     *
     * @param PingPushRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping(PingPushRequest $request)
    {
        $data = $request->validated();

        $this->pushService->ping($data['endpoint'], auth()->id());

        return response()->json(['ok' => true]);
    }

    /**
     * 웹 푸시 구독 존재 여부 확인
     *
     * @param ExistsPushRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(ExistsPushRequest $request)
    {
        $data = $request->validated();

        $exists = $this->pushService->exists(
            $data['endpoint'],
            auth()->id()
        );

        return response()->json(['exists' => $exists]);
    }

    /**
     * 푸시 클릭 추적
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function open(Request $request, string $token)
    {
        $redirectUrl = $this->pushService->open($token);

        return redirect()->to($redirectUrl);
    }

    /**
     * 사용자 대상 푸시 발송
     *
     * @param SendPushToUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendToUser(SendPushToUserRequest $request)
    {
        $data = $request->validated();

        $result = $this->pushService->sendToUser($data, $request->userAgent());

        return response()->json($result);
    }

}
