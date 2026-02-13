# 📘 게시판 개발 스킬 규격 (Board Skill Spec) — TH-Study-Group 기준

## 0) 목표 (Goal)
- 게시판 CRUD를 **Controller / Service / Repository** 계층 분리로 개발한다.
- 권한은 Policy로 처리한다.
- 유효성 검사는 FormRequest로 처리한다.
- 게시글(Post)은 모든 CRUD/조회 시 이력 기록.
- 댓글(Comment)은 **단독 페이지에서 발생하는 CRUD/조회만 이력 기록**,  
  게시글 상세 페이지에서 include로 불러오는 댓글은 **이력 기록 제외**.
- 게시글 등록 시 필요 시 관리자에게 메일 알림(Queue).
- **등록/수정 페이지는 동일 Blade**에서 처리한다.
- **리스트는 paginate(20)** 고정.
- 게시글 CRUD/메일/페이지 조회 등 주요 흐름은 `Log::info()` 기록한다.
- 퍼블리싱은 Bootstrap 5 + 게시판 CSS 규칙을 따른다.

---

# 1) 기본 아키텍처 규칙

## Must
- Controller → request/response/view
- Service → 비즈니스 로직
- Repository → DB 쿼리
- Policy → 권한 체크
- FormRequest → 유효성 검사
- 등록/수정 페이지는 하나의 Blade로 처리
- 게시글 CRUD → 이력 + 로그 Must
- 댓글 CRUD → 단독 페이지에서만 이력/로그 Must (include는 제외)
- 모든 예외는 try/catch + error 로그 + 사용자 메시지

---

# 2) 프론트 유효성 UX 규칙

## Must — 상태(status) 영역
```blade
@if ($errors->any())
  <div class="alert alert-danger">에러가 있습니다. 확인해 주세요.</div>
@endif

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif
```

## Must — 각 필드
- old() 유지
- @error 출력
- is-invalid 적용
- 등록/수정 페이지 동일 Blade

---

# 3) 게시글(Post) 상태 규칙

## 공통
- `post_type` 필수
- status 기본값: **wait**
- use_flag 기본값: **1**

---

# 4) 게시판 유형별 status / use_flag 규칙 (최종)

## A) 공지사항 / 자유게시판 (고객 노출형)
### Must
- `status = wait` 기본값 유지
- 고객 노출 제어는 **use_flag(1/0)** 로 처리
  - use_flag = 1 → 고객 노출
  - use_flag = 0 → 고객 미노출

## B) 문의사항(1:1) / 처리·승인 절차 게시판
### Must
- `use_flag = 1` 기본값
- 처리 상태는 **status(wait 등)** 로 제어
  - 예: wait / processing / done / reject

---

# 5) 댓글(Comment) 규칙

## 댓글 이력 규칙 (최종)
### Must — 단독 페이지에서만 기록
- `/comments/{id}` 형태로 댓글 단독 조회/CRUD
→ 등록/수정/삭제/조회 모두 이력 + 로그 기록

### Never — include 댓글
- 게시글 상세(`/posts/{id}/show`)에 포함되는 댓글
→ 이력/로그 기록하지 않음

---

# 6) 이력 테이블 규칙 (th_post_histories)

## A) 게시글(Post) — Must
### 필수 컬럼
- job_type: 등록 / 수정 / 삭제 / 조회
- table_type: `th_posts`
- post_type
- create_datetime
- create_user_idx

### 대상 ID 규칙
- post_idx 등 존재 시 저장
- 없으면 스키마 변경 없음

## B) 댓글(Comment) — Must(단독 페이지 한정)
### 필수 컬럼
- job_type: 등록 / 수정 / 삭제 / 조회
- table_type: `comment`
- post_type: 부모 게시글 post_type
- create_datetime
- create_user_idx

### include 댓글 조회는 기록하지 않음

---

# 7) 리스트 + 페이징 규칙 (중요)

## Must — paginate(20)
모든 게시판 목록은 다음처럼 사용한다:

```php
$posts = Post::paginate(20);
```

## Must — Bootstrap 기반 페이징
Blade:
```blade
{{ $posts->links() }}
```

---

## 📌 페이징 뷰 종류 (custom / simple)

### 1) Custom Pagination View (번호 기반)
- 기본 `paginate()`는 Custom 뷰를 사용한다.
- 번호 기반으로 노출된다.
- 모바일은 번호 범위를 더 적게, PC는 더 많이 보이게 된다.
  - 모바일: 짧은 범위의 페이지 번호
  - PC: 더 많은 페이지 번호

### 2) Simple Pagination View (이전/다음만)
- `simplePaginate(20)` 사용 시 Simple 뷰를 사용한다.
- 모바일에서는 **이전/다음만 표시(페이지 번호 없음)**

---

## ✅ 페이징 설정 위치: PaginationServiceProvider.php

### Must
- 프로젝트 공통 페이징 뷰 설정은 `PaginationServiceProvider.php`에서 관리한다.
- Custom 뷰와 Simple 뷰를 각각 기본값으로 지정한다.

### 예시 코드
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class PaginationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Custom (번호 기반)
        Paginator::defaultView('pagination::bootstrap-5');

        // Simple (이전/다음만)
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
    }
}
```

### 사용 규칙 요약
- `paginate(20)` → Custom(번호 기반, 모바일은 범위 축소/PC는 범위 확대)
- `simplePaginate(20)` → Simple(모바일은 이전/다음만)

---

# 8) 메일 알림 규칙 (Queue)

## Must
- 게시글 등록 시 메일 발송
- 댓글 등록은 필요 시 적용 (기본 제외)
- 관리자(level=admin) 최대 3명
- 발송 여부 미지정 시 먼저 질문 후 구현
- SendMailJob 큐 사용

---

# 9) 메일 템플릿 규칙

## Must
- 위치: `app/Mail/`
- 제목: `[사이트명] 게시글 제목 일부...`
- 본문은 너무 길면 `...`
- 하단에 게시글 링크 포함

---

# 10) 로그(Log) 규칙

## 게시글 CRUD 로그 (Must)
```php
Log::info('[Post][Create] success', [
  'user_idx' => $user->idx ?? null,
  'post_idx' => $post->idx ?? null,
  'post_type' => $post->post_type ?? null,
  'status' => $post->status ?? null,
  'use_flag' => $post->use_flag ?? null,
  'ip' => request()->ip(),
]);
```

## 댓글 단독 CRUD 로그 (Must)
```php
Log::info('[Comment][Action]', [
  'user_idx' => $user->idx ?? null,
  'comment_idx' => $comment->idx ?? null,
  'post_idx' => $comment->post_idx ?? null,
  'ip' => request()->ip(),
]);
```

## include 댓글 — Never
이력/로그 생성하지 않음

## 예외 처리
```php
catch (\Throwable $e) {
  Log::error('[Error]', ['message' => $e->getMessage()]);
  return back()->withErrors(['status' => '오류가 발생했습니다.'])->withInput();
}
```

---

# 11) 게시판 퍼블리싱 규칙 (Bootstrap 5)

## Must
- 넘침 처리: `board-ellipsis`
- 모바일 숨김: `board-col-hidden`
- 줄바꿈 방지: `text-nowrap`
- 정렬: text-start / text-center / text-end
- 상태 뱃지:
```blade
<span class="badge text-bg-{{ $badgeClass }}">상태</span>
```

---

# 12) CSS 스펙

## Must
- `.board-col-hidden` = 모바일 숨김
- `.board-ellipsis` = 텍스트 말줄임(...)
- `.text-nowrap` = 줄바꿈 방지
- 정렬 클래스 = text-start / text-center / text-end

---

# 13) Acceptance Criteria (수용 기준)

## 게시글 CRUD
- [ ] th_posts 저장
- [ ] 등록/수정/삭제/조회 이력 기록(table_type=th_posts)
- [ ] 공지/자유 = use_flag로 노출 제어
- [ ] 문의/승인형 = status로 처리상태 제어
- [ ] FormRequest 정상 동작
- [ ] status / error / invalid / old() 출력
- [ ] 로그(info) 기록

## 댓글 CRUD
- [ ] 단독 페이지에서 CRUD → 이력 + 로그 Must
- [ ] include 댓글은 이력/로그 제외

## 리스트
- [ ] paginate(20)
- [ ] Bootstrap 페이징(custom 또는 simple 규칙 반영)
- [ ] links() 출력
- [ ] ellipsis + nowrap + 모바일 숨김 적용
- [ ] 페이지 로그(info)

## 메일
- [ ] 게시글 등록 시 관리자 최대 3명 큐 발송
- [ ] 제목/본문 줄임(...)
- [ ] 링크 포함
- [ ] 메일 로그(info)
