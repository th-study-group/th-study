<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteTagRequest;
use App\Http\Resources\Mcp\Tools\NoteTagResource;
use App\Services\Api\NoteTagService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * 노트 태그 MCP API 컨트롤러
 */
class NoteTagController extends Controller
{
    public function __construct(
        private readonly NoteTagService $noteTagService
    ) {}

    /**
     * 노트 태그 목록 반환
     *
     * @param NoteTagRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/note-tags',
        summary: '태그 조회',
        description: '태그명과 노트 분류 조건으로 노트 태그 목록을 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tag'],
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
                        property: 'topic_code',
                        type: 'string',
                        example: '라라벨'
                    ),
                    new OA\Property(
                        property: 'tag',
                        type: 'string',
                        example: 'Swagger'
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
                description: '태그 조회 성공',
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
                            example: '노트 태그 목록 조회 성공'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'tag_name',
                                        type: 'string',
                                        example: 'Swagger'
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
                                    new OA\Property(
                                        property: 'note',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(
                                                    property: 'group_name',
                                                    type: 'string',
                                                    example: '블로그'
                                                ),
                                                new OA\Property(
                                                    property: 'categories_name',
                                                    type: 'string',
                                                    example: '개발'
                                                ),
                                                new OA\Property(
                                                    property: 'topic_name',
                                                    type: 'string',
                                                    example: '라라벨'
                                                ),
                                                new OA\Property(
                                                    property: 'note_idx',
                                                    type: 'integer',
                                                    example: 1
                                                ),
                                                new OA\Property(
                                                    property: 'subject',
                                                    type: 'string',
                                                    example: 'Laravel Swagger 적용 방법'
                                                ),
                                            ],
                                            type: 'object'
                                        )
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
    public function index(NoteTagRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $noteTags = $this->noteTagService->getNoteTags($data);

        return response()->json([
            'success' => true,
            'message' => '노트 태그 목록 조회 성공',
            'data' => NoteTagResource::collection(
                $noteTags->items()
            ),
            'pagination' => [
                'current_page' => $noteTags->currentPage(),
                'per_page' => $noteTags->perPage(),
                'total' => $noteTags->total(),
                'last_page' => $noteTags->lastPage(),
                'has_more' => $noteTags->hasMorePages(),
                'next_page' => $noteTags->hasMorePages()
                    ? $noteTags->currentPage() + 1 : null,
            ],
        ]);
    }
}
