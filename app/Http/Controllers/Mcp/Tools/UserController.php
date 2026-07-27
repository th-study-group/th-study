<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\UserRequest;
use App\Http\Resources\Mcp\Tools\UserResource;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: '/api/mcp/tools/users',
        summary: '사용자 조회',
        description: '조건에 맞는 사용자 목록을 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
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
                    new OA\Property(
                        property: 'idx',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: '이태희'
                    ),
                    new OA\Property(
                        property: 'nick_name',
                        type: 'string',
                        example: '광개토태왕'
                    ),
                    new OA\Property(
                        property: 'birth_year',
                        type: 'integer',
                        example: 1989
                    ),
                    new OA\Property(
                        property: 'sex',
                        type: 'string',
                        enum: ['M', 'W'],
                        example: 'M'
                    ),
                    new OA\Property(
                        property: 'marketing_info_agree',
                        type: 'integer',
                        enum: [0, 1],
                        example: 1
                    ),
                    new OA\Property(
                        property: 'level',
                        type: 'string',
                        enum: ['normal', 'admin'],
                        example: 'admin'
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '사용자 조회 성공'
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
