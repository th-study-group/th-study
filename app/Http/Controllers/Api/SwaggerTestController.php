<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * Swagger 테스트 API 컨트롤러
 */
class SwaggerTestController extends Controller
{
    #[OA\Get(
        path: '/api/swagger-test',
        summary: 'Swagger 테스트',
        tags: ['테스트'],
        security: [
            ['bearerAuth' => []],
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '정상 응답',
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
                            example: 'Swagger test success'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Swagger test success',
        ]);
    }
}
