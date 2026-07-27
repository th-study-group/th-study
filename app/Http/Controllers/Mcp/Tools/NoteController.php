<?php

namespace App\Http\Controllers\Mcp\Tools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\Tools\NoteRequest;
use App\Http\Resources\Mcp\Tools\NoteResource;
use App\Services\Api\NoteService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * 블로그 MCP API 컨트롤러
 */
class NoteController extends Controller
{
    public function __construct(
        private readonly NoteService $noteService
    ) {}
    
    /**
     * 블로그 글 목록 반환 
     *
     * @param NoteRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/mcp/tools/notes',
        summary: '노트 조회',
        description: '검색 조건에 맞는 노트 목록을 조회합니다.',
        tags: ['MCP'],
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'subject',
                        type: 'string',
                        example: '라라벨'
                    ),
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
                        example: 'laravel'
                    ),
                    new OA\Property(
                        property: 'note_idx',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'has_thumbnail',
                        type: 'boolean',
                        example: true
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
                description: '노트 조회 성공',
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
                            example: '노트 목록 조회 성공'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'note_idx',
                                        type: 'integer',
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: 'subject',
                                        type: 'string',
                                        example: 'Laravel 12 Swagger 적용'
                                    ),
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
                                        example: 'Laravel'
                                    ),
                                    new OA\Property(
                                        property: 'thumbnail',
                                        type: 'string',
                                        nullable: true,
                                        example: 'https://example.com/thumbnail.jpg'
                                    ),
                                    new OA\Property(
                                        property: 'create_datetime',
                                        type: 'string',
                                        example: '2026-07-27 12:00:00'
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
                                    example: 100
                                ),
                                new OA\Property(
                                    property: 'last_page',
                                    type: 'integer',
                                    example: 5
                                ),
                                new OA\Property(
                                    property: 'has_more',
                                    type: 'boolean',
                                    example: true
                                ),
                                new OA\Property(
                                    property: 'next_page',
                                    type: 'integer',
                                    nullable: true,
                                    example: 2
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
    public function index(NoteRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $notes = $this->noteService->getBlogs($data);
    
        return response()->json([
            'success' => true,
            'message' => '노트 목록 조회 성공',
            'data' => NoteResource::collection(
                $notes->items()
            ),
            'pagination' => [
                'current_page' => $notes->currentPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
                'last_page' => $notes->lastPage(),
                'has_more' => $notes->hasMorePages(),
                'next_page' => $notes->hasMorePages()
                    ? $notes->currentPage() + 1 : null,
            ],
        ]);
    }
}
