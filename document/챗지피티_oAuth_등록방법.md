# 티에이치스터디 MCP Postman 테스트 + ChatGPT OAuth 등록 정리

## 1. Postman 공통 헤더

```text
Accept: application/json
Content-Type: application/json
```

Bearer 토큰이 필요한 요청은 아래도 추가합니다.

```text
Authorization: Bearer JWT_ACCESS_TOKEN
```

---

## 2. JWT 로그인 테스트

요청

```http
POST http://localhost:8000/api/mcp/login
```

Body

```json
{
  "email": "이메일",
  "password": "비밀번호"
}
```

정상 응답

```json
{
  "access_token": "JWT_ACCESS_TOKEN",
  "refresh_token": "JWT_REFRESH_TOKEN",
  "token_type": "Bearer",
  "expires_in": 1800
}
```

---

## 3. JWT refresh 테스트

요청

```http
POST http://localhost:8000/api/mcp/refresh
```

Body

```json
{
  "refresh_token": "JWT_REFRESH_TOKEN"
}
```

정상 응답

```json
{
  "access_token": "NEW_JWT_ACCESS_TOKEN",
  "token_type": "Bearer",
  "expires_in": 1800
}
```

---

## 4. OAuth authorize 테스트

브라우저 접속

```text
http://localhost:8000/mcp/oauth/authorize?client_id=thstudy-chatgpt&redirect_uri=http://localhost:8000/mcp/oauth-test&response_type=code&state=test123
```

정상 흐름

```text
로그인 화면 이동
→ 로그인 성공
→ redirect_uri로 authorization code 전달
```

결과 예시

```text
http://localhost:8000/mcp/oauth-test?code=AUTHORIZATION_CODE&state=test123
```

---

## 5. OAuth token 발급 테스트

요청

```http
POST http://localhost:8000/api/mcp/oauth/token
```

Body

```json
{
  "grant_type": "authorization_code",
  "client_id": "thstudy-chatgpt",
  "client_secret": "OAUTH_CLIENT_SECRET",
  "code": "AUTHORIZATION_CODE"
}
```

정상 응답

```json
{
  "access_token": "JWT_ACCESS_TOKEN",
  "refresh_token": "JWT_REFRESH_TOKEN",
  "token_type": "Bearer",
  "expires_in": 1800
}
```

---

## 6. OAuth refresh 테스트

요청

```http
POST http://localhost:8000/api/mcp/oauth/token
```

Body

```json
{
  "grant_type": "refresh_token",
  "client_id": "thstudy-chatgpt",
  "client_secret": "OAUTH_CLIENT_SECRET",
  "refresh_token": "JWT_REFRESH_TOKEN"
}
```

정상 응답

```json
{
  "access_token": "NEW_JWT_ACCESS_TOKEN",
  "token_type": "Bearer",
  "expires_in": 1800
}
```

---

## 7. MCP initialize 테스트

요청

```http
POST http://localhost:8000/api/mcp
```

Header

```text
Authorization: Bearer JWT_ACCESS_TOKEN
```

Body

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "initialize"
}
```

---

## 8. MCP tools/list 테스트

요청

```http
POST http://localhost:8000/api/mcp
```

Header

```text
Authorization: Bearer JWT_ACCESS_TOKEN
```

Body

```json
{
  "jsonrpc": "2.0",
  "id": 2,
  "method": "tools/list"
}
```

---

## 9. MCP tools/call 테스트

요청

```http
POST http://localhost:8000/api/mcp
```

Header

```text
Authorization: Bearer JWT_ACCESS_TOKEN
```

Body

```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "method": "tools/call",
  "params": {
    "name": "blog_search",
    "arguments": {
      "title": "라라벨",
      "limit": 20
    }
  }
}
```

---

# ChatGPT MCP OAuth 앱 등록

## 앱 이름

```text
티에이치스터디
```

## 설명

```text
티에이치스터디의 개발, SEO, 여행, 경제 콘텐츠를 검색하고 조회할 수 있는 MCP 서버입니다.
```

## MCP 서버 URL

```text
http://localhost:8000/api/mcp
```

## 인증 방식

```text
OAuth
```

## 클라이언트 등록 방식

```text
사용자 정의 OAuth 클라이언트
```

주의:

```text
동적 클라이언트 등록(DCR) 선택하면 안 됨
```

---

## 인증 URL

```text
http://localhost:8000/mcp/oauth/authorize
```

## 토큰 URL

```text
http://localhost:8000/api/mcp/oauth/token
```

## 등록 URL

```text
http://localhost:8000
```

## 인증 서버 기본

```text
http://localhost:8000
```

## 리소스

```text
http://localhost:8000/api/mcp
```

## Client ID

```text
thstudy-chatgpt
```

## Client Secret

```text
OAUTH_CLIENT_SECRET 값
```

## 범위(scope)

```text
비워둠
```

---

# OAUTH_CLIENT_SECRET 정책

현재 구조는 OAuth 서버가 자동 발급하는 방식이 아니라 직접 랜덤 문자열을 생성해서 사용하는 구조입니다.

즉:

```text
개발자가 직접 랜덤 문자열 생성
→ .env 저장
→ config/mcp.php 연결
```

구조입니다.

---

# OAUTH_CLIENT_SECRET 발급 방법

artisan tinker 실행

```bash
php artisan tinker
```

랜덤 문자열 생성

```php
Illuminate\Support\Str::random(128)
```

또는

```php
Str::random(128)
```

출력 예시

```text
bK39B2K9EBsbZa4CezsuO0lb3lkiz7VR37F7kSvVEkufdUbuYuXPks4sjd804FR9L1p822kDTUY4TaIgrjfV7gQOinFtkXnL5PHgQaXwBwmC9BFlzbii41rFFobztpGi
```

---

# .env 설정

```env
OAUTH_CLIENT_SECRET=생성한랜덤문자열
```

예시

```env
OAUTH_CLIENT_SECRET=bK39B2K9EBsbZa4CezsuO0lb3lkiz7VR37F7kSvVEkufdUbuYuXPks4sjd804FR9L1p822kDTUY4TaIgrjfV7gQOinFtkXnL5PHgQaXwBwmC9BFlzbii41rFFobztpGi
```

---

# config/mcp.php 연결

```php
<?php

return [

    'oauth' => [
        'client_id' => env('OAUTH_CLIENT_ID', 'thstudy-chatgpt'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'code_ttl' => (int) env('OAUTH_CODE_TTL', 5),
    ],

    'tool_path' => base_path('mcp/tool.json'),
];
```

---

# JWT 정책

현재 JWT 구조는 아래와 같습니다.

## access_token

```text
30분
```

## refresh_token

```text
14일
```

예시

```env
JWT_TTL=30
JWT_REFRESH_TTL=20160
```

---

# refresh_token 정책

정상 흐름

```text
access_token 만료
→ refresh_token으로 새 access_token 발급
```

refresh_token까지 만료되면:

```text
invalid_grant
```

응답 후 다시 로그인해야 합니다.

즉:

```text
refresh_token 만료
→ OAuth authorize 다시 진행
→ 새 authorization_code 발급
→ 새 access_token / refresh_token 발급
```

구조입니다.

---

# 현재 최종 상태

```text
OAuth 연결 성공
JWT 로그인 성공
OAuth token 발급 성공
refresh_token 발급 성공
MCP initialize 성공
tools/list 성공
tools/call 성공
ChatGPT MCP 연결 성공
```