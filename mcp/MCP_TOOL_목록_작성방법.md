# MCP Tool 작성 가이드

## 목적

MCP Tool은 GPT가 자연어를 API 요청 JSON으로 변환하여 호출하는 구조입니다.

Tool 정의는 가능한 단순하게 유지하고, inputSchema.description에 검색 방식과 예시를 명확하게 작성하는 것을 권장합니다.

---

# Tool 기본 구조

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

---

# Tool 최상위 필드

## name

Tool 고유 이름입니다.

중복되지 않아야 합니다.

snake_case 사용을 권장합니다.

예시

```json
{
  "name": "blog_search"
}
```

```json
{
  "name": "note_search"
}
```

```json
{
  "name": "member_search"
}
```

---

## description

Tool 설명입니다.

GPT가 Tool 선택 시 참고합니다.

조회 대상과 검색 조건을 포함하는 것이 좋습니다.

예시

```json
{
  "description": "블로그 게시글을 제목, 상태, 작성일 기준으로 조회합니다."
}
```

좋은 예시

```json
{
  "description": "회원 정보를 이름, 이메일, 가입일 기준으로 조회합니다."
}
```

나쁜 예시

```json
{
  "description": "조회"
}
```

---

## url

실제 API 주소입니다.

예시

```json
{
  "url": "/api/mcp/tools/blog-search"
}
```

```json
{
  "url": "/api/mcp/tools/member-search"
}
```

---

## method

HTTP Method 입니다.

예시

```json
{
  "method": "POST"
}
```

사용 가능 값

```text
GET
POST
PUT
PATCH
DELETE
```

검색 Tool은 POST 사용을 권장합니다.

---

## levels

Tool 접근 권한입니다.

예시

```json
{
  "levels": [
    "admin"
  ]
}
```

복수 권한 예시

```json
{
  "levels": [
    "admin",
    "normal"
  ]
}
```

---

# x-meta

GPT가 Tool을 이해하기 위한 보조 정보입니다.

필수는 아니지만 권장합니다.

---

## domain

Tool 카테고리

예시

```json
{
  "domain": "blogs"
}
```

가능 예시

```text
notes
members
orders
products
systems
logs
boards
```

---

## keywords

GPT Tool 선택용 키워드

예시

```json
{
  "keywords": [
    "블로그",
    "게시글",
    "글검색",
    "SEO",
    "라라벨"
  ]
}
```

사용자 자연어와 최대한 비슷하게 작성합니다.

---

## comment

Tool 설명

예시

```json
{
  "comment": "블로그 게시글을 제목, 상태, 작성일 기준으로 조회합니다."
}
```

---

## fieldLabels

결과 출력용 라벨

예시

```json
{
  "fieldLabels": {
    "title": "제목",
    "status": "상태",
    "created_at": "작성일"
  }
}
```

---

# inputSchema

GPT가 실제 API 요청 JSON을 생성할 때 사용하는 핵심 정의입니다.

---

## 기본 구조

```json
{
  "inputSchema": {
    "type": "object",
    "properties": {},
    "required": []
  }
}
```

---

## type

입력 타입

검색 Tool은 대부분 object 사용

```json
{
  "type": "object"
}
```

---

## properties

입력 가능한 필드 정의

```json
{
  "properties": {}
}
```

---

## required

필수 입력값

```json
{
  "required": []
}
```

특정 값이 반드시 필요한 경우

```json
{
  "required": [
    "member_idx"
  ]
}
```

---

# 검색 연산자

| 연산자 | 의미 |
|----------|----------|
| = | 정확히 일치 |
| != | 일치하지 않음 |
| LIKE | 포함 검색 |
| NOT LIKE | 포함 제외 |
| > | 초과 |
| >= | 이상 |
| < | 미만 |
| <= | 이하 |
| IN | 여러 값 중 하나 |
| NOT IN | 여러 값 제외 |
| IS NULL | 값 없음 |
| IS NOT NULL | 값 있음 |
| BETWEEN | 범위 검색 |

---

# 문자열 검색

## 정확히 일치 검색 (=)

```json
{
  "status": {
    "type": "string",
    "enum": [
      "published",
      "draft",
      "hidden",
      "deleted"
    ],
    "description": "게시글 상태 정확히 일치 검색(=). 예: published"
  }
}
```

사용자 요청

```text
공개글 보여줘
```

GPT 요청

```json
{
  "status": "published"
}
```

실제 SQL

```sql
status = 'published'
```

---

## 포함 검색 (LIKE)

```json
{
  "title": {
    "type": "string",
    "description": "게시글 제목 포함 검색(LIKE '%keyword%'). 예: 라라벨"
  }
}
```

사용자 요청

```text
라라벨 글 찾아줘
```

GPT 요청

```json
{
  "title": "라라벨"
}
```

실제 SQL

```sql
title LIKE '%라라벨%'
```

---

## 제외 검색 (NOT LIKE)

```json
{
  "title_not_like": {
    "type": "string",
    "description": "게시글 제목 제외 검색(NOT LIKE). 예: FastAPI"
  }
}
```

실제 SQL

```sql
title NOT LIKE '%FastAPI%'
```

---

# IN 검색

```json
{
  "status_in": {
    "type": "array",
    "items": {
      "type": "string"
    },
    "description": "여러 상태 중 하나라도 일치(IN). 예: ['published','draft']"
  }
}
```

사용자 요청

```text
공개글 또는 임시저장 글 보여줘
```

GPT 요청

```json
{
  "status_in": [
    "published",
    "draft"
  ]
}
```

실제 SQL

```sql
status IN ('published','draft')
```

---

# NOT IN 검색

```json
{
  "status_not_in": {
    "type": "array",
    "items": {
      "type": "string"
    },
    "description": "특정 상태 제외(NOT IN). 예: ['deleted','hidden']"
  }
}
```

사용자 요청

```text
삭제된 글 제외
```

GPT 요청

```json
{
  "status_not_in": [
    "deleted"
  ]
}
```

실제 SQL

```sql
status NOT IN ('deleted')
```

---

# 날짜 검색

## 특정 날짜

```json
{
  "created_at": {
    "type": "string",
    "description": "특정 작성일 검색(=). 형식: YYYY-MM-DD. 예: 2026-05-19"
  }
}
```

---

## 시작일

```json
{
  "created_at_from": {
    "type": "string",
    "description": "작성일 시작일 검색(>=). 형식: YYYY-MM-DD. 예: 2026-05-01"
  }
}
```

---

## 종료일

```json
{
  "created_at_to": {
    "type": "string",
    "description": "작성일 종료일 검색(<=). 형식: YYYY-MM-DD. 예: 2026-05-31"
  }
}
```

---

## 기간 검색

```json
{
  "created_at_from": "2026-05-01",
  "created_at_to": "2026-05-31"
}
```

실제 SQL

```sql
created_at >= '2026-05-01'
AND created_at <= '2026-05-31'
```

사용자 요청

```text
2026년 5월 글 보여줘
```

---

# 숫자 검색

## 최소값

```json
{
  "view_count_min": {
    "type": "integer",
    "description": "최소 조회수 검색(>=). 예: 100"
  }
}
```

---

## 최대값

```json
{
  "view_count_max": {
    "type": "integer",
    "description": "최대 조회수 검색(<=). 예: 1000"
  }
}
```

---

## 범위 검색

```json
{
  "view_count_min": 100,
  "view_count_max": 1000
}
```

실제 SQL

```sql
view_count >= 100
AND view_count <= 1000
```

---

# NULL 검색

```json
{
  "is_deleted": {
    "type": "boolean",
    "description": "삭제 여부 검색. false는 삭제안됨(IS NULL), true는 삭제됨(IS NOT NULL)"
  }
}
```

---

# 정렬

```json
{
  "sort_by": {
    "type": "string",
    "description": "정렬 컬럼. 예: created_at, updated_at, view_count"
  }
}
```

```json
{
  "sort_dir": {
    "type": "string",
    "enum": [
      "asc",
      "desc"
    ],
    "description": "정렬 방향"
  }
}
```

사용 예시

```json
{
  "sort_by": "created_at",
  "sort_dir": "desc"
}
```

실제 SQL

```sql
ORDER BY created_at DESC
```

---

# 페이지네이션

## page

```json
{
  "page": {
    "type": "integer",
    "description": "조회 페이지 번호. 기본값 1"
  }
}
```

---

## per_page

```json
{
  "per_page": {
    "type": "integer",
    "description": "페이지당 조회 개수. 기본값 20. 최대값 100"
  }
}
```

---

사용 예시

```json
{
  "page": 2,
  "per_page": 20
}
```

실제 SQL

```sql
LIMIT 20 OFFSET 20
```

---

# 자주 사용하는 공통 필드

```json
{
  "idx": {
    "type": "integer",
    "description": "고유 번호(PK)"
  },

  "title": {
    "type": "string",
    "description": "제목 포함 검색(LIKE)"
  },

  "subject": {
    "type": "string",
    "description": "제목 포함 검색(LIKE)"
  },

  "name": {
    "type": "string",
    "description": "이름 포함 검색(LIKE)"
  },

  "status": {
    "type": "string",
    "description": "상태 정확히 일치 검색(=)"
  },

  "created_at": {
    "type": "string",
    "description": "등록일"
  },

  "updated_at": {
    "type": "string",
    "description": "수정일"
  },

  "page": {
    "type": "integer",
    "description": "페이지 번호"
  },

  "per_page": {
    "type": "integer",
    "description": "페이지당 조회 개수"
  }
}
```