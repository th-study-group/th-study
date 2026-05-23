# TH-STUDY MCP Postman 테스트 + ChatGPT OAuth 등록 정리

## 1. Postman 공통 헤더

```text
Accept: application/json
Content-Type: application/json
```

Bearer 토큰이 필요한 요청은 아래도 추가합니다.

```text
Authorization: Bearer JWT_ACCESS_TOKEN
```

## 2. JWT 로그인 테스트

요청

```http
POST https://www.th-study.com/api/mcp/login
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

## 3. JWT refresh 테스트

요청

```http
POST https://www.th-study.com/api/mcp/refresh
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

## 5. OAuth token 발급 테스트

요청

```http
POST https://www.th-study.com/api/mcp/oauth/token
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

## 6. OAuth refresh 테스트

요청

```http
POST https://www.th-study.com/api/mcp/oauth/token
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

## 7. MCP initialize 테스트

요청

```http
POST https://www.th-study.com/api/mcp
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

## 8. MCP tools/list 테스트

요청

```http
POST https://www.th-study.com/api/mcp
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

## 9. MCP tools/call 테스트

요청

```http
POST https://www.th-study.com/api/mcp
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
https://www.th-study.com/api/mcp
```

## 인증 방식

```text
OAuth
```

## 클라이언트 등록 방식

```text
사용자 정의 OAuth 클라이언트
```

## 인증 URL

```text
https://www.th-study.com/mcp/oauth/authorize
```

## 토큰 URL

```text
https://www.th-study.com/api/mcp/oauth/token
```

## 등록 URL

```text
https://www.th-study.com
```

## 인증 서버 기본

```text
https://www.th-study.com
```

## 리소스

```text
https://www.th-study.com/api/mcp
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

## 주의

```text
동적 클라이언트 등록(DCR) 선택하지 말고 사용자 정의 OAuth 클라이언트를 선택합니다.
```