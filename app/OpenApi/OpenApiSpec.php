<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: '티에이치스터디 API',
    description: '티에이치스터디 API 문서',
    contact: new OA\Contact(
        name: '티에이치스터디',
        email: 'admin@th-study.com'
    )
)]
#[OA\Server(
    url: '/',
    description: '현재 서버'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: '로컬 서버'
)]
#[OA\Server(
    url: 'http://localhost:8080',
    description: '도커 서버'
)]
#[OA\Server(
    url: 'https://www.th-study.com',
    description: '운영 서버'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class OpenApiSpec
{
}