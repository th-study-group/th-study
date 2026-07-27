<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteCategoriesRequest;
use App\Http\Resources\Mcp\Tools\NoteCategoriesResource;
use App\Services\Api\NoteCategoriesService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;


/**
 * 노트 카테고리 MCP API 컨트롤러
 */
class NoteCategoriesController extends Controller
{
    public function __construct(
        private readonly NoteCategoriesService $noteCategoriesService
    ) {}

    /**
     * 노트 카테고리 반환 목록
     * 
     * @param NoteCategoriesRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/note-categories',
        summary: '카테고리 조회',
        description: '그룹 하위의 노트 카테고리 목록을 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['group_code'],
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
                        property: 'categories_name',
                        type: 'string',
                        example: '개발'
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
                description: '카테고리 조회 성공',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: '노트 카테고리 목록 조회 성공'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
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
                                        property: 'categories_name',
                                        type: 'string',
                                        example: '개발'
                                    ),
                                    new OA\Property(
                                        property: 'categories_memo',
                                        type: 'string',
                                        example: '개발 관련 콘텐츠'
                                    ),
                                    new OA\Property(
                                        property: 'create_datetime',
                                        type: 'string',
                                        example: '2026-07-27 12:00:00'
                                    ),
                                    new OA\Property(
                                        property: 'create_user_idx',
                                        type: 'integer',
                                        example: 1
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(
                            property: 'pagination',
                            properties: [
                                new OA\Property(
                                    property: 'current_page',
                                    type: 'integer',
                                    example: 1
                                ),
                                new OA\Property(
                                    property: 'per_page',
                                    type: 'integer',
                                    example: 20
                                ),
                                new OA\Property(
                                    property: 'total',
                                    type: 'integer',
                                    example: 5
                                ),
                                new OA\Property(
                                    property: 'last_page',
                                    type: 'integer',
                                    example: 1
                                ),
                                new OA\Property(
                                    property: 'has_more',
                                    type: 'boolean',
                                    example: false
                                ),
                                new OA\Property(
                                    property: 'next_page',
                                    type: 'integer',
                                    nullable: true,
                                    example: null
                                ),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
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
    public function index(NoteCategoriesRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteCategories = $this->noteCategoriesService->getNoteCategories($data);

        return response()->json([
            'success' => true,
            'message' => '노트 카테고리 목록 조회 성공',
            'data' => NoteCategoriesResource::collection(
                $noteCategories->items()
            ),
            'pagination' => [
                'current_page' => $noteCategories->currentPage(),
                'per_page' => $noteCategories->perPage(),
                'total' => $noteCategories->total(),
                'last_page' => $noteCategories->lastPage(),
                'has_more' => $noteCategories->hasMorePages(),
                'next_page' => $noteCategories->hasMorePages()
                    ? $noteCategories->currentPage() + 1 : null,
            ],
        ]);
    }
}
