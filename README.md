# 티에이치 스터디 - 개발자성장플랫폼

Laravel 기반 개인 개발 플랫폼입니다.  
기록, 운영, 커뮤니케이션 기능을 하나로 묶어 성장 과정을 서비스 형태로 관리합니다.

이 문서는 기능이 커져도 빠르게 구조를 파악할 수 있도록 **핵심 정책과 동작 원리만** 정리합니다.

## 1. 핵심 기능

- 회원가입, 로그인/로그아웃, 이메일 인증
- 비밀번호 찾기(메일 링크), 비밀번호 변경(요청/완료)
- 사용자 계정 관리(내 정보 수정, 탈퇴)
- 공지 게시판(사용자 조회, 관리자 CRUD/공개여부 관리)
- 문의 게시판(사용자 등록/조회, 관리자 상태 관리)
- 비로그인 게스트 문의 접수 및 관리자 처리
- 댓글(일반 게시글 및 운영 답변 흐름)
- 메일 발송/수신 로그, 로그인 로그, 게시글 히스토리 로그

### 브랜드 슬로건

- 나를 뛰어넘는 개발자
- 나를 뛰어넘는 성장하는 개발자
- 개발을 넘어 브랜드 기획, 디자인, 광고, 애드센스까지 확장하는 개발자

### `home.blade.php` / `intro.blade.php` 역할

- `resources/views/home.blade.php`: 대외 첫인상 랜딩 페이지
- `resources/views/intro.blade.php`: 브랜드 철학과 경험을 서사형으로 전달하는 소개 페이지
- 운영 기준: `home`은 빠른 이해, `intro`는 깊은 공감과 신뢰 형성에 초점

## 2. 기술 스택

- Backend: PHP 8.2, Laravel 10
- DB: MySQL
- Frontend: Blade, Bootstrap 5, jQuery
- Date UI: Flatpickr
- Queue: Database queue
- Realtime(실험): Pusher + Laravel Echo
- Infra: Docker(app, queue, nginx, mysql, node), self-hosted deploy

## 3. 디렉터리 빠른 가이드

```text
app/
  Http/Controllers   # 요청 진입점
  Http/Requests      # 유효성 검증(FormRequest)
  Services           # 비즈니스 로직
  Repositories       # DB 접근 로직
  Models             # Eloquent 모델
  Policies           # 권한 판단
  Middleware         # 요청 정책
  Events/Listeners   # 로그/이력 이벤트 처리
  Jobs               # 비동기 작업(메일 큐)

routes/
  web.php            # 홈/정적 페이지
  auth.php           # 인증/계정 관련
  login.php          # 로그인 사용자 영역
  user.php           # 공개 게시판/게스트문의
  admin.php          # 관리자 영역
  content.php        # 카테고리형 콘텐츠 페이지
  dev.php            # 로컬 전용 테스트 라우트

resources/views/
  layouts/           # 공통 레이아웃
  users/             # 회원/인증 화면
  inquiries/         # 사용자 문의 화면
  admins/            # 관리자 화면
  emails/            # 메일 템플릿
```

## 4. 요청 처리 원리

기본 흐름은 아래와 같습니다.

1. `Route`가 URL을 컨트롤러로 연결
2. `FormRequest`가 입력 검증
3. `Controller`가 `Service` 호출
4. `Service`가 도메인 규칙 처리
5. `Repository`가 DB 조회/저장 수행
6. 결과를 Blade 또는 JSON으로 응답

핵심 원칙:

- Controller는 얇게 유지
- 도메인 규칙은 Service에 집중
- DB 쿼리는 Repository에 분리

## 5. 라우팅/접근 정책

- 공통 웹: `routes/web.php`
- 인증: `routes/auth.php`
- 로그인 사용자: `routes/login.php`
- 관리자: `routes/admin.php`
- 공개 게시판/게스트문의: `routes/user.php`
- 로컬 개발 테스트: `routes/dev.php` (`local.only` 미들웨어)

관리자 라우트는 `auth + email.verified + level:admin` 정책으로 보호됩니다.

## 6. 게시판 타입 정책

게시판 타입 정책은 `config/board.php`로 중앙 관리합니다.

- `post_type`: 전체 게시판 타입 정의
- `post_type_for_route`: 사용자 URL 노출 대상 타입
- `post_type_excluded`: 권한/정책 제외 타입
- `post_use_flag`: 공개(1)/비공개(0) 라벨
- `status`: 문의 처리 상태(`wait`, `in_progress`, `on_hold`, `completed`)

운영 관점:

- 타입을 추가/변경할 때 `config/board.php`를 기준으로 라우트/뷰/권한을 함께 맞춥니다.
- 관리자에서 게시글 공개 여부(`use_flag`)를 제어합니다.

## 7. 미들웨어/권한 정책

### 미들웨어

- `auth`: 로그인 사용자만 접근
- `email.verified`: 이메일 인증 완료 사용자만 접근
- `level:admin`: 관리자 권한 체크
- `note.slug`: 콘텐츠 slug 유효성 체크
- `local.only`: 로컬 환경/허용 IP에서만 접근
- `ForcePasswordChange`: 비밀번호 변경 강제 대상 유저 제어
- `CheckSessionVersion`: 개인정보 변경 후 세션 불일치 시 재로그인 강제

### Policy

- `UserPolicy`: 사용자 탈퇴 권한
- `PostPolicy`: 게시글 수정/삭제/공개여부 변경 권한
- `CommentPolicy`: 댓글 생성/수정/삭제 권한

핵심 원칙:

- 미들웨어는 "입구 보안"
- Policy는 "행동 권한"

## 8. 메일 시스템

### 8.1 공통 발송 파이프라인

1. 컨트롤러/서비스에서 `SendMailJob` 실행
2. 큐 워커가 메일 발송 수행
3. 발송 직후 `MailSentEvent` 발생
4. `WriteMailLogEventListener`가 `mail_logs` 기록
5. 일부 기능은 메일 링크 진입 시 수신 시각/IP를 업데이트

### 8.2 메일 템플릿 구성

템플릿 경로: `resources/views/emails`

- `verify_code.blade.php`: 회원가입 이메일 인증
- `reset_password.blade.php`: 비밀번호 재설정 링크
- `password_change_request.blade.php`: 비밀번호 변경 요청 안내
- `password_change_complete.blade.php`: 비밀번호 변경 완료 안내
- `withdrawal_notice.blade.php`: 회원탈퇴 완료 안내
- `inquiry_created.blade.php`: 문의 등록 알림(관리자)
- `inquiry_answered.blade.php`: 문의 답변 알림(작성자)

### 8.3 메일 발송 시나리오

- 회원가입 후 이메일 인증 메일 발송
- 계정찾기(비밀번호 찾기) 요청 시 재설정 메일 발송
- 비밀번호 변경 요청/완료 시 안내 메일 발송
- 회원탈퇴 완료 시 안내 메일 발송
- 문의 등록 시 관리자 알림 메일 발송
- 댓글(답변) 등록 시 작성자 알림 메일 발송

### 8.4 메일 로그 정책

`mail_logs`에 아래 정보를 기록합니다.

- 메일 종류(`kind`)
- 수신 이메일(`email`)
- 토큰(`token`, 필요 시)
- 발송 시각(`send_datetime`)
- 수신 확인 시각/아이피(`receive_datetime`, `receive_ip`)

운영자는 발송 이력과 링크 도달 이력을 분리해서 추적할 수 있습니다.

## 9. 데이터 모델 핵심

- `users`: 회원
- `posts`: 게시글/문의
- `comments`: 댓글
- `guest_posts`: 비로그인 문의
- `post_histories`: 게시글 작업 이력
- `login_logs`: 로그인 시도 로그
- `mail_logs`: 메일 발송/수신 로그
- `jobs`, `failed_jobs`, `sessions`, `password_reset_tokens`: 큐/세션/복구

## 10. 로컬 실행

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

큐 사용 시:

```bash
php artisan queue:work
```

프론트 자산 개발:

```bash
npm install
npm run dev
```

## 11. Docker 실행 개요

`docker-compose.yml` 기준 서비스:

- `app` (php-fpm)
- `queue` (queue worker)
- `nginx`
- `mysql`
- `node` (vite dev server)

## 12. 운영 보안 가이드

- `.env` 및 민감한 키/토큰/패스워드는 저장소에 커밋하지 않습니다.
- README에는 실제 운영 계정, 실제 도메인 내부정보, 비밀키를 기록하지 않습니다.
- 로컬 개발용 라우트(`_dev/*`)는 운영에서 노출되지 않도록 환경 정책을 유지합니다.
- 메일/로그는 운영 추적용으로만 활용하고 개인정보 최소 수집 원칙을 지킵니다.

## 13. 기능 확장 시 체크리스트

기능 추가 시 아래 순서로 보면 소스 탐색이 빠릅니다.

1. `routes/*`에서 엔드포인트 위치 확인
2. 대응 `Controller` 확인
3. `Request` 검증 규칙 확인
4. `Service` 도메인 규칙 확인
5. `Repository`/`Model` DB 처리 확인
6. `Policy`/`Middleware` 접근 정책 확인
7. `views/*` 화면 및 `emails/*` 템플릿 확인

이 순서를 기준으로 보면 기능이 커져도 추적 경로가 안정적으로 유지됩니다.

## 14. 홈/인트로 퍼블리싱 가이드

향후 퍼블리싱 업데이트 시 아래 기준을 유지하면 브랜드 일관성을 지키기 쉽습니다.

### 14.1 페이지 역할 분리

- `home`: 서비스 핵심 요약, 신뢰 요소, CTA, 문의 전환
- `intro`: 섹션 전환 기반 스토리텔링, 약력/철학/확장 방향 강조

### 14.2 콘텐츠 구조 참고

- `home` 주요 블록
  - Hero(브랜드 키메시지 + 주요 CTA)
  - About(프로젝트 존재 이유)
  - Highlights/Keywords(핵심 가치)
  - Slogan/Why(브랜드 톤 강화)
  - Stats/Roadmap(운영 관점 미래 계획)
  - Profile/Stack(신뢰 근거)
  - Contact Modal + Guest Inquiry(전환 지점)
- `intro` 주요 블록
  - Hero(타이핑 슬로건)
  - 소개, 약력, 사회공헌, PHP, AI, 수익, 문의
  - 우측 Dot Navigation + 섹션별 이미지/태그/액션 버튼

### 14.3 스타일 시스템 참고

- `public/css/intro/home.css`
  - 다크 톤 그라디언트 배경 + 강조 색상(`--accent`, `--accent2`)
  - 카드/칩/버튼 컴포넌트 중심 구성
  - `reveal` 클래스로 섹션 등장 연출
- `public/css/intro/intro.css`
  - 풀스크린 섹션 전환, Dot Nav, Hero 배경 패럴랙스
  - 태그/액션 버튼/리스트의 일관된 시각 규칙
  - 모바일/접근성(`prefers-reduced-motion`) 대응 포함

### 14.4 인터랙션 구현 참고

- `public/js/intro/home.js`
  - 앵커 스무스 스크롤
  - `IntersectionObserver` 기반 reveal 애니메이션
- `public/js/intro/intro.js`
  - 섹션 전환(휠/터치/키보드)
  - 타이핑 효과(`나를 뛰어넘는 개발자`)
  - 이미지/배경 패럴랙스
  - 내장 SVG 아이콘 동적 주입

### 14.5 브랜드 이미지/자산 운영 가이드

- 홈 핵심 이미지: `public/images/main_logo.png`, `public/images/extension_logo.png`, `public/images/intro_project_img.jpg`
- 소개 섹션 이미지: `public/images/intro/*.avif`
- 업데이트 권장 방식
  - 새 이미지 추가 시 기존 명명 규칙(`intro/001.avif` 형태) 유지
  - 텍스트 변경 시 슬로건/키워드/CTA를 함께 조정
  - 섹션 삭제/추가 시 `home.js`, `intro.js`의 앵커/네비게이션 동작 동시 점검
  - 문구는 "성장 + 확장 + 운영" 3축을 유지

## 15. 마무리 한 줄

티에이치스터디그룹은 하나의 사이트가 아니라, 개발자로서의 성장 과정을 담는 플랫폼이다.
