<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteGroupRequest;
use App\Services\Api\NoteGroupService;
use App\Http\Resources\Mcp\Tools\NoteGroupResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * 노트 그룹 MCP API 컨트롤러
 */
class NoteGroupController extends Controller
{
    public function __construct(
        private readonly NoteGroupService $noteGroupService
    ) {}

    #[OA\Post(
        path: '/api/mcp/tools/note-groups',
        summary: '그룹 조회',
        description: '노트의 최상위 분류인 그룹 목록을 조회합니다. 그룹 코드 또는 그룹명을 조건으로 검색할 수 있습니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: 'group_code',
                        description: '그룹 코드 정확히 일치 검색',
                        type: 'string',
                        maxLength: 20,
                        nullable: true,
                        example: 'blog'
                    ),
                    new OA\Property(
                        property: 'group_name',
                        description: '그룹명 포함 검색',
                        type: 'string',
                        maxLength: 30,
                        nullable: true,
                        example: '블로그'
                    ),
                    new OA\Property(
                        property: 'page',
                        description: '조회 페이지 번호',
                        type: 'integer',
                        minimum: 1,
                        default: 1,
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'per_page',
                        description: '페이지당 조회 개수',
                        type: 'integer',
                        minimum: 1,
                        maximum: 100,
                        default: 20,
                        nullable: true,
                        example: 20
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '그룹 조회 성공',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: '노트 그룹 목록 조회 성공'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(
                                        property: 'group_code',
                                        description: '그룹 코드',
                                        type: 'string',
                                        example: 'blog'
                                    ),
                                    new OA\Property(
                                        property: 'group_name',
                                        description: '그룹명',
                                        type: 'string',
                                        example: '블로그'
                                    ),
                                    new OA\Property(
                                        property: 'create_datetime',
                                        description: '등록일시',
                                        type: 'string',
                                        example: '2026-07-26 15:00:00'
                                    ),
                                    new OA\Property(
                                        property: 'create_user_idx',
                                        description: '등록자 사용자 IDX',
                                        type: 'integer',
                                        example: 1
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'pagination',
                            type: 'object',
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
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: '인증 실패',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: '유효성 검사 실패',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: false
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: '유효성 검사 실패'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                'group_code' => [
                                    '그룹 코드는 20자를 초과할 수 없습니다.',
                                ],
                                'per_page' => [
                                    '페이지당 조회 개수는 100 이하이어야 합니다.',
                                ],
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(NoteGroupRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteGroups = $this->noteGroupService->getNoteGroups($data);

        return response()->json([
            'success' => true,
            'message' => '노트 그룹 목록 조회 성공',
            'data' => NoteGroupResource::collection(
                $noteGroups->items()
            ),
            'pagination' => [
                'current_page' => $noteGroups->currentPage(),
                'per_page' => $noteGroups->perPage(),
                'total' => $noteGroups->total(),
                'last_page' => $noteGroups->lastPage(),
                'has_more' => $noteGroups->hasMorePages(),
                'next_page' => $noteGroups->hasMorePages()
                    ? $noteGroups->currentPage() + 1 : null,
            ],
        ]);
    }
}
