# TH-STUDY 백엔드 개발 규칙

## 1. 적용 원칙

- 일반적인 Laravel 관례보다 현재 TH-STUDY의 Migration, Model, Route, Service, Repository 구현을 우선한다.
- 기능 구현 전 관련 Migration과 `agent_rules/database_schema.md`를 확인한다. 차이가 있으면 실제 Migration과 현재 코드를 우선한다.
- 이 문서는 현재 반복 사용되는 공통 패턴만 정리한다. 기능 전용 상태값, 이력, 메일 정책은 `board.md`, `note.md`를 따른다.

## 2. 실행 환경

- PHP 8.2 / Laravel 12 / Node 20을 사용한다.
- `composer.json`에는 Laravel, Sanctum, JWT, Swagger, Intervention Image, Web Push 등이 포함되어 있다. 새 패키지 도입 전에는 기존 패키지와 구현 사용처를 먼저 확인한다.

## 3. Route와 Middleware

- Route는 용도별 파일로 분리한다. 기본 web은 `routes/web.php`, 인증은 `routes/auth.php`, 로그인 사용자 기능은 `routes/login.php`, 콘텐츠는 `routes/content.php`, 관리자는 `routes/admin.php`, API/MCP는 `routes/api*.php`와 `routes/mcp.php`를 확인한다.
- `RouteServiceProvider`에서 공통 그룹을 적용한다.
  - 일반 web: `web`
  - 콘텐츠: `web`, `note.slug`
  - 로그인 사용자: `web`, `auth`, `email.verified`
  - 관리자: `web`, `auth`, `email.verified`, `level:admin`, `/admins` prefix, `admins.` name prefix
- `{idx}`는 전역 숫자 패턴이 적용되어 있다. 라우트 파라미터, route name, prefix/group 구조는 유사 기능의 기존 정의를 우선 재사용한다.
- 공통 web middleware에는 CSRF, 세션, 비밀번호 변경 강제, 세션 버전 확인, 접근 로그, Analytics 공유가 포함된다.

## 4. Controller / Service / Repository

- CRUD 도메인은 기본적으로 Controller → Service → Repository로 나눈다.
  - Controller: FormRequest 입력 수신, `validated()`/`safe()` 값 추출, Policy 호출, view/redirect/JSON 응답 조립
  - Service: 도메인 로직, 감사 사용자 정보, 트랜잭션, 로그, Event/Job 호출
  - Repository: Model 생성·수정, 조회·검색·정렬·페이지네이션 쿼리
- Service와 Repository는 생성자 주입을 사용한다.
- 예외도 존재한다. 인증/비밀번호 재설정 등 일부 Controller는 모델 직접 조회나 Job dispatch를 수행하고, API/MCP는 별도 `Services/Api`, `Repositories/Api`, Resource 계층을 사용한다. 유사 기능의 현재 구조를 확인한 뒤 같은 범위에서 따른다.

## 5. Validation과 권한

- 입력 검증은 `app/Http/Requests`의 FormRequest를 우선 사용한다.
- 다수의 FormRequest는 `failedValidation()`에서 `Log::info()`로 action, model, ip, user_idx, errors를 남긴 뒤 `back()->withErrors()->withInput()`으로 되돌린다.
- FormRequest의 `authorize()` 구현 여부는 현재 혼재되어 있다. 해당 Request와 Controller/Policy 호출 위치를 함께 확인하고 기존 흐름을 유지한다.
- 권한은 `AuthServiceProvider`에 등록한 `User`, `Post`, `Comment`, `Note` Policy를 사용한다. Controller에서는 `$this->authorize()` 또는 사용자 `can()` 확인이 사용된다.
- 관리자/인증/이메일 인증 같은 진입 권한은 route middleware와 Policy를 함께 사용한다.

## 6. Database / Model Convention

- MySQL 연결에는 `th_` table prefix가 설정되어 있다. Migration과 Model의 table name은 prefix를 제외한 이름을 사용한다.
- 일반 업무 테이블의 기본 PK는 대체로 `idx` bigint auto increment다. 사용자 참조와 감사 정보는 `user_idx`, `create_user_idx`, `update_user_idx`, `delete_user_idx`를 사용한다.
- 일반 업무 테이블의 시각 컬럼은 `create_datetime`, `update_datetime`, `delete_datetime`이다. Laravel의 `created_at`/`updated_at`를 임의로 추가하지 않는다.
- `Post`, `Comment`, `GuestPost`, Note 계열처럼 감사·삭제 컬럼을 가진 모델은 `Base`를 상속한다.
  - `Base`는 primary key `idx`, `$timestamps = false`, SoftDeletes, 생성/수정 시각 자동 설정을 제공한다.
  - 삭제 시각은 SoftDeletes가 `delete_datetime`을 사용한다.
- 로그, 통계, push, history, mapping 모델과 `User`는 Base를 상속하지 않는 경우가 있다. 이들은 실제 Model의 `$table`, `$primaryKey`, `$timestamps`, casts, 복합키 처리 등을 그대로 따른다.
- Laravel 인프라 테이블(`jobs`, `failed_jobs`, `sessions`, `password_reset_tokens`)에는 `id`, `user_id`, `created_at` 등이 공존한다. 업무 테이블 규칙을 일괄 적용하지 않는다.

## 7. CRUD 감사 정보와 삭제

- 생성 시 필요한 경우 `create_user_idx`, 수정 시 `update_user_idx`, 삭제 시 `delete_user_idx`를 현재 사용자 idx로 기록한다.
- Base 계열 삭제는 보통 삭제 사용자 정보를 먼저 저장한 뒤 `$model->delete()`로 Soft Delete 한다.
- 인증 사용자 취득은 `Auth::id()`, `auth()->id()`, `$request->user()`가 공존한다. 변경하는 기존 코드의 방식을 유지한다.
- `use_flag` 표현은 도메인별로 다르다. Post는 `0/1`, Note는 Model accessor를 통한 `Y/N` 표현을 사용하므로 서로 변환 규칙을 강제하지 않는다.

## 8. Query / Relation / Pagination

- 일반 조회는 Eloquent를 사용한다. 화면 또는 응답에서 관계를 사용할 때 Repository에서 `with()`로 eager loading 한다.
- 관계 조건 검색에는 `whereHas()`, 선택 조건에는 `when()`, 목록 정렬에는 주로 `orderByDesc('idx')`를 사용한다.
- 집계·업서트 성격의 traffic 처리에는 Query Builder의 `DB::table()`, `selectRaw()`, `upsert()`가 사용된다.
- 목록은 `paginate()`를 사용하고 Controller에서 `$paginator->appends($filters)`로 검색 조건을 유지하는 흐름이 반복된다.
- 페이지 크기는 도메인별로 다르다. 게시판 계열은 보통 20, 노트는 10을 사용한다. 전역 고정값으로 통일하지 않는다.
- `PaginationServiceProvider`와 `config/pagination.php`가 full/simple pagination view를 설정한다. 현재 애플리케이션 코드에는 `simplePaginate()` 사용처가 없다.

## 9. Transaction / Log / Exception

- 등록·수정·삭제처럼 여러 DB 변경, 감사 정보, 태그/매핑, 이력이 함께 발생하는 Service 작업은 `DB::transaction()`을 사용한다.
- 트랜잭션 이후에만 수행해야 하는 파일 삭제는 `DB::afterCommit()`을 사용한다.
- 주요 Service 흐름은 `Log::info('[Domain][Action] ...', context)`로 기록한다. context에는 해당 도메인의 idx, user_idx, ip 등 추적에 필요한 값을 포함한다.
- 실패 또는 비정상 흐름에는 `Log::warning()` 또는 `Log::error()`가 사용된다. 확인한 정상 흐름 로그는 사용자 식별자·대상 idx·IP 중심이며 비밀번호나 token 원문은 기록하지 않는다. 다만 일부 예외 처리에는 exception message가 기록되므로 민감정보 로그 정책을 별도 공통 규칙으로 단정하지 않는다.
- try/catch는 GuestPost, 회원·비밀번호, MCP 등 일부 흐름에서 사용되지만 모든 CRUD에 공통 적용되어 있지는 않다. 유사 기능의 예외·응답 흐름을 우선한다.

## 10. Event / Queue / File

- Post와 Note 이력은 Service에서 Event를 발행하고 Listener가 history table에 저장한다. Event-Listener 매핑은 `EventServiceProvider`에 명시한다.
- 메일·웹 푸시·노트 이미지 후처리는 `ShouldQueue` Job으로 구현되어 있다. 기존 `SendMailJob`, `SendWebPushJob`, `NoteImageProcessingJob` 사용처를 우선 확인한다.
- 노트 썸네일은 `Storage::disk('public')`을 사용하며 업로드·실제 파일 삭제·후처리 Job이 노트 도메인에 구현되어 있다. 다른 기능에 그대로 일반화하지 않는다.

## 11. Response와 테스트

- 일반 웹 성공 흐름은 `to_route()` 또는 `redirect()->with('status', ...)`를 주로 사용한다.
- AJAX는 `response()->json()`으로 기능에 필요한 값과 `message`를 반환한다. JSON 공통 envelope는 현재 모든 API에 통일되어 있지 않다.
- MCP/API 응답은 해당 Controller, Resource, OpenAPI 정의의 기존 형식을 따른다.
- 현재 tests는 Laravel 기본 예제 수준이며 도메인별 테스트 패턴은 정립되어 있지 않다. 새 테스트 구조를 임의로 규칙화하지 않는다.
