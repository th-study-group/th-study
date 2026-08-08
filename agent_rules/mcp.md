# MCP 개발 규칙

## 1. 목적

이 문서는 TH-STUDY의 MCP Tool, MCP API 및 관련 기능을 개발하거나 수정할 때 적용하는 공통 규칙이다.

MCP 작업에서는 일반적인 MCP 구현 방식을 임의로 적용하지 않는다.

반드시 현재 TH-STUDY의 기존 MCP 구조와 실제 구현을 먼저 확인하고 동일한 패턴을 우선한다.


# 2. 작업 전 확인

MCP 관련 작업에서는 작업 계획을 작성하기 전에 다음을 확인한다.

1. `AGENTS.md`
2. `agent_rules/backend.md`
3. `agent_rules/database_schema.md`
4. 기존 MCP Route
5. 기존 MCP Controller / Service / Repository
6. 기존 MCP Tool 정의
7. 기존 인증 및 권한 처리
8. 요청 대상과 유사한 기존 Tool

화면 변경이 포함되는 경우 추가로 다음을 확인한다.

- `agent_rules/frontend.md`

게시판 또는 노트 등 기능별 MCP 작업이면 해당 기능 규칙도 함께 확인한다.

예:

- 게시판 MCP → `agent_rules/board.md`
- 노트 MCP → `agent_rules/note.md`


# 3. 기존 구현 우선

새로운 MCP Tool을 만들기 전에 기존 Tool 중 유사한 구현을 먼저 찾는다.

다음 항목을 확인한다.

- Tool name
- description
- URL
- HTTP Method
- levels
- x-meta
- inputSchema
- Controller
- Service
- Repository
- Response 구조
- 인증/권한
- Validation
- Logging
- Pagination

기존 패턴과 다른 구조가 필요한 경우 임의로 변경하지 않고 작업 계획에 이유를 명시한다.


# 4. Tool 기본 구조

현재 프로젝트에서 사용하는 Tool 정의 구조를 따른다.

기본적으로 다음 항목을 확인한다.

```json
{
  "name": "blog_search",
  "description": "블로그 게시글 조회",
  "url": "/api/mcp/tools/blog-search",
  "method": "POST",
  "levels": [
    "admin"
  ],
  "x-meta": {},
  "inputSchema": {}
}
```

실제 작업에서는 기존 Tool 정의를 확인하여 현재 프로젝트 구조와 일치시킨다.


# 5. Tool name

Tool name은 기존 프로젝트 네이밍 규칙을 따른다.

새로운 이름이 필요한 경우 명확하고 짧게 작성한다.

기본적으로 snake_case를 사용한다.

예:

```text
blog_search
note_search
member_search
```

동일하거나 유사한 역할의 기존 Tool이 존재하면 새로운 Tool을 중복 생성하지 않는다.


# 6. description

description은 GPT가 Tool의 용도를 판단할 수 있도록 작성한다.

다음 내용을 짧고 명확하게 포함한다.

- 무엇을 조회하거나 처리하는 Tool인지
- 주요 검색 또는 처리 대상

모호한 설명을 사용하지 않는다.

나쁜 예:

```text
조회
```

좋은 예:

```text
블로그 게시글을 제목, 상태, 작성일 기준으로 조회합니다.
```


# 7. levels

Tool 접근 권한은 기존 인증 및 회원 권한 구조를 확인한 뒤 설정한다.

예:

```json
{
  "levels": [
    "admin"
  ]
}
```

필요하지 않은 권한을 임의로 추가하지 않는다.

기존 Middleware, 인증 구조 및 사용자 Level 정의와 일치해야 한다.


# 8. x-meta

`x-meta`는 GPT의 Tool 선택과 결과 이해를 돕는 보조 정보로 사용한다.

현재 프로젝트에서 사용하는 항목을 우선한다.

주요 항목:

- domain
- keywords
- comment
- fieldLabels

예:

```json
{
  "domain": "blogs",
  "keywords": [
    "블로그",
    "게시글",
    "글검색"
  ],
  "comment": "블로그 게시글을 조회합니다.",
  "fieldLabels": {
    "title": "제목",
    "status": "상태"
  }
}
```

불필요한 메타데이터를 임의로 추가하지 않는다.


# 9. inputSchema

`inputSchema`는 GPT가 자연어 요청을 실제 API 요청 JSON으로 변환할 때 사용하는 계약이다.

Schema는 가능한 단순하게 유지한다.

기본 구조:

```json
{
  "type": "object",
  "properties": {},
  "required": []
}
```

필드 정의에는 다음 내용을 명확하게 작성한다.

- 입력 타입
- 검색 의미
- 필요한 경우 검색 연산자
- 값 형식
- 필요한 경우 간단한 예시

GPT가 필드 의미를 추측해야 하는 Schema를 만들지 않는다.


# 10. 검색 필드

검색 필드는 실제 Repository 또는 Query에서 지원하는 조건만 정의한다.

Schema에만 존재하고 Backend에서 처리하지 않는 검색 조건을 추가하지 않는다.

반대로 Backend에서 지원하지만 GPT가 사용할 수 없는 조건이 필요한 경우 Tool Schema 반영 여부를 검토한다.

대표적인 검색 표현:

- 정확히 일치 → `=`
- 포함 → `LIKE`
- 제외 → `NOT LIKE`
- 여러 값 → `IN`
- 여러 값 제외 → `NOT IN`
- 최소값 → `>=`
- 최대값 → `<=`
- 범위 → `BETWEEN` 또는 from/to
- NULL 여부 → `IS NULL` / `IS NOT NULL`

필드 이름과 실제 동작이 일치해야 한다.


# 11. 문자열 검색

포함 검색 필드는 description에서 포함 검색임을 명확하게 표시한다.

예:

```json
{
  "title": {
    "type": "string",
    "description": "게시글 제목 포함 검색(LIKE). 예: 라라벨"
  }
}
```

정확히 일치하는 상태값 등은 가능한 경우 enum을 사용한다.

예:

```json
{
  "status": {
    "type": "string",
    "enum": [
      "published",
      "draft"
    ],
    "description": "게시글 상태 정확히 일치 검색(=)"
  }
}
```

enum 값은 실제 코드와 DB에서 허용하는 값을 확인한 뒤 정의한다.


# 12. 범위 검색

날짜와 숫자 범위는 의미가 명확한 필드명을 사용한다.

예:

```text
created_at_from
created_at_to

view_count_min
view_count_max
```

description에 다음을 명시한다.

- 비교 방향
- 입력 형식
- 필요한 경우 예시

날짜 형식은 기존 API가 실제 지원하는 형식을 따른다.


# 13. 배열 검색

여러 값 검색에는 array를 사용한다.

예:

```json
{
  "status_in": {
    "type": "array",
    "items": {
      "type": "string"
    },
    "description": "여러 상태 중 하나라도 일치(IN)"
  }
}
```

실제 Backend가 IN 검색을 지원하는 경우에만 Schema에 추가한다.


# 14. NULL 검색

NULL 여부를 검색해야 하는 경우 의미가 명확한 boolean 필드를 우선 검토한다.

예:

```json
{
  "is_deleted": {
    "type": "boolean",
    "description": "삭제 여부. false는 삭제되지 않은 데이터, true는 삭제된 데이터"
  }
}
```

실제 Soft Delete 및 삭제 컬럼 구조는 `agent_rules/database_schema.md`와 Model을 확인한다.


# 15. 정렬

정렬 기능이 필요한 경우 Backend에서 허용하는 컬럼만 사용할 수 있도록 제한한다.

가능하면 `sort_by`는 실제 허용 컬럼을 enum으로 정의한다.

`sort_dir`은 다음 값으로 제한한다.

```json
{
  "sort_dir": {
    "type": "string",
    "enum": [
      "asc",
      "desc"
    ]
  }
}
```

사용자가 전달한 컬럼명을 그대로 SQL 정렬에 사용하지 않는다.


# 16. 페이지네이션

목록 Tool은 기존 MCP 구현에서 페이지네이션을 사용하는지 먼저 확인한다.

사용하는 경우 일반적으로 다음 필드를 사용한다.

```text
page
per_page
```

기본값과 최대값은 실제 Backend Validation 및 기존 MCP 구현을 따른다.

Schema와 실제 API의 제한값이 다르지 않도록 한다.


# 17. Database 연계

MCP Tool이 DB 데이터를 조회하거나 변경하면 반드시 다음을 함께 확인한다.

- `agent_rules/database_schema.md`
- 관련 Model
- 관련 Migration
- 관련 Repository

Laravel 기본 convention을 추측하지 않는다.

예:

```text
id
created_at
user_id
```

등을 당연히 존재한다고 가정하지 않는다.

TH-STUDY의 실제 PK, 사용자 참조 컬럼, datetime 및 Soft Delete 구조를 확인한다.


# 18. Backend 연계

MCP API 구현은 `agent_rules/backend.md`의 공통 규칙을 따른다.

기존 MCP에 별도 Service / Repository / Resource 구조가 존재하면 해당 구조를 우선한다.

일반 웹 CRUD 구조를 MCP에 그대로 복사하지 않는다.

MCP의 기존 Response 구조, 인증 방식, Validation 및 Error 처리를 확인한다.


# 19. 자연어 요청 기준

Tool Schema는 사용자가 자연어로 요청했을 때 GPT가 적절한 JSON을 만들 수 있도록 작성한다.

예:

사용자:

```text
라라벨 관련 블로그 글 찾아줘
```

Tool 요청:

```json
{
  "title": "라라벨"
}
```

사용자:

```text
최근 공개 글 보여줘
```

Tool 요청 예:

```json
{
  "status": "published",
  "sort_by": "created_at",
  "sort_dir": "desc"
}
```

단, 예시는 실제 Tool이 해당 필드를 지원하는 경우에만 작성한다.


# 20. Tool Schema 작성 원칙

Tool Schema 작성 시 다음 원칙을 따른다.

1. Schema는 가능한 단순하게 유지한다.
2. GPT가 이해하기 쉬운 필드명을 사용한다.
3. description은 짧고 구체적으로 작성한다.
4. 실제 Backend에서 지원하는 조건만 노출한다.
5. enum은 실제 허용값을 확인한다.
6. 검색 연산자의 의미가 필드명과 description에서 명확해야 한다.
7. 불필요한 검색 옵션을 한 Tool에 과도하게 추가하지 않는다.
8. 기존 Tool과 중복되는 기능을 만들지 않는다.
9. 기존 MCP 응답 구조와 인증 구조를 유지한다.
10. Schema와 Backend 구현이 서로 다른 상태로 남지 않도록 한다.


# 21. MCP 작업 계획

MCP 관련 작업 계획에는 일반 작업 계획 외에 다음 내용을 포함한다.

- 수정 또는 생성할 Tool
- Tool name
- URL / Method
- 필요한 levels
- inputSchema 변경 여부
- Backend 변경 여부
- Database 영향 여부
- 기존 Tool 재사용 가능 여부
- 자연어 요청 예시
- 예상 Tool 요청 JSON
- 테스트 방법

신규 Tool이라면 기존 유사 Tool과 어떤 부분을 재사용하는지도 설명한다.


# 22. MCP 테스트

MCP Tool 변경 후 가능한 범위에서 다음을 검증한다.

1. Tool 정의 JSON 유효성
2. inputSchema 유효성
3. Route 연결
4. 인증 및 levels
5. Validation
6. Controller / Service / Repository 연결
7. 검색 조건
8. 정렬
9. 페이지네이션
10. Response 구조
11. 자연어 요청에 대응하는 Tool 입력 예시

Schema만 수정하고 실제 Backend 동작과 맞는지 확인하지 않은 상태로 완료 처리하지 않는다.


# 23. 금지 사항

다음 행동을 하지 않는다.

- 기존 MCP 구조를 확인하지 않고 신규 Tool 생성
- 실제 DB 컬럼을 확인하지 않고 Schema 필드 생성
- Backend에서 지원하지 않는 검색 연산자 노출
- 사용자 입력을 그대로 SQL 컬럼이나 정렬 기준으로 사용
- 실제 허용값을 확인하지 않은 enum 작성
- 기존 Tool과 동일한 기능의 Tool 중복 생성
- 필요 이상으로 거대한 inputSchema 작성
- MCP와 일반 웹 API 구조가 같다고 가정
- 기존 인증/권한 구조를 무시한 levels 설정
- Schema 변경 후 Backend 정합성 확인 생략


# 24. 핵심 원칙

MCP 작업은 다음 순서를 따른다.

**기존 Tool 확인 → 관련 규칙 확인 → Backend/DB 확인 → Schema 설계 → 작업 계획 → 승인 → 구현 → MCP 테스트**

Tool 정의는 GPT가 이해하기 쉬워야 하지만,
항상 실제 TH-STUDY Backend 구현과 일치해야 한다.