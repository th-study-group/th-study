<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\UserRequest;
use App\Http\Resources\Mcp\Tools\UserResource;
use App\Services\Mcp\Tools\UserService;
use Illuminate\Http\JsonResponse;

/**
 * 사용자 MCP API 컨트롤러
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {} 

    /**
     * 사용자 목록 반환
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function index(UserRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $users = $this->userService->getUsers($data);

        return response()->json([
            'success' => true,
            'message' => '사용자 목록 조회 성공',
            'data' => UserResource::collection(
                $users->items()
            ),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'has_more' => $users->hasMorePages(),
                'next_page' => $users->hasMorePages()
                    ? $users->currentPage() + 1 : null,
            ],
        ]);
    }
}
