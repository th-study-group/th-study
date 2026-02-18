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

### 프론트 라이브러리 구성(`app.blade.php` 기준)

로딩 기준 파일:
- `resources/views/layouts/app.blade.php`
- `resources/views/partials/head-styles.blade.php`
- `resources/views/partials/head-scripts.blade.php`

사용 중인 라이브러리:
- UI Framework: Bootstrap 5 (`public/css/bootstrap.min.css`, `public/js/bootstrap.bundle.min.js`)
- DOM/이벤트: jQuery 3.7.1 (`public/js/jquery-3.7.1.min.js`)
- Date Picker: Flatpickr (CDN)
- Icon: Bootstrap Icons (CDN)
- Tech Icon: Devicon (CDN)

외부 에셋/아이콘 출처:
- Unsplash: `https://unsplash.com/ko`
- Devicon: `https://devicon.dev/`

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

### 8.5 PWA 설치부터 푸시 동작 흐름

웹 푸시는 설치/구독/발송/클릭 추적까지 한 흐름으로 동작합니다.

1. PWA 설치
- 브라우저에서 사이트를 설치(PWA)하면 서비스워커가 활성화됩니다.
- 설치 앱과 일반 브라우저 탭은 저장소/세션 컨텍스트가 다를 수 있습니다.

2. 로그인 시 자동 구독 동기화
- `public/js/pwa_push.js`의 `autoSyncOnLogin()`이 실행됩니다.
- 구독이 없으면 `subscribeAndSave()`로 새 구독을 만들고 서버(`/push/subscribe`)에 저장합니다.
- 구독이 있으면 `/push/exists`로 서버 존재 여부를 확인하고, 없으면 재등록합니다.

2-1. 앱 푸시 허용 팝업(Standalone 앱 전용)
- `public/js/pwa_push.js`의 `openNativePushPermissionPrompt()`가 실행됩니다.
- 로그인 상태 + 홈화면 추가로 실행된 PWA 컨텍스트(standalone)에서만 팝업을 노출합니다.
- 허용 버튼 클릭 시 `Notification.requestPermission()` 호출 후 구독 생성/저장을 진행합니다.

2-2. OS별 푸시 알림 설정 위치(사용자 안내용)
- iOS: `설정 > 알림 > 티에이치스터디(PWA 앱명)`에서 알림 허용/배너/사운드 설정
- Android(Chrome PWA): `설정 > 앱 > TH Study(또는 브라우저 앱) > 알림`에서 허용/차단
- Android(브라우저 권한): `Chrome > 사이트 설정 > 알림 > th-study.com`에서 허용/차단
- 최초 PWA 실행 시 허용을 놓친 경우 위 경로에서 수동으로 다시 켤 수 있습니다.

3. 구독 유지/정리 정책
- 활성 구독은 `web_push_subscriptions`에 저장됩니다.
- 로그아웃/회원탈퇴 시 해당 사용자의 구독을 서버에서 전체 삭제합니다.
- 프론트도 가능하면 브라우저 구독 해제를 함께 시도합니다.

4. 푸시 발송
- 서비스 레이어에서 `PushService::sendToUser()`를 호출합니다.
- 대상 사용자별 `SendWebPushJob`이 큐에 등록되고, Job에서 실제 웹푸시를 전송합니다.
- 발송 이력은 `web_push_messages`에 기록됩니다.

5. 클릭 추적 및 이동
- 푸시 payload URL은 `/push/open/{click_token}` 형태로 전달됩니다.
- 클릭 시 `click_datetime`이 기록되고, `target_url`로 리다이렉트됩니다.
- 로그인 만료 시에는 로그인 후 intended 경로로 복귀합니다.

6. iPhone 캐시 대응(운영 반영 안정화)
- iOS Safari/PWA는 JS/Service Worker 캐시가 강하게 남을 수 있어, 정적 에셋 버전 파라미터를 적용합니다.
- `resources/views/partials/head-scripts.blade.php`와 `resources/views/partials/head-styles.blade.php`에서 `filemtime(...)` 기반 `?v=`를 사용합니다.
- `resources/views/layouts/app.blade.php`의 서비스워커 등록 URL에도 `?v=`를 붙이고 `reg.update()`를 호출합니다.
- 배포 시 `php artisan optimize:clear`를 실행해 서버 캐시를 정리합니다.

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

프론트 자산(개발):

```bash
npm install
npm run dev
```

프론트 자산(운영 배포용 빌드):

```bash
npm install
npm run build
```

## 11. Docker 실행 개요

`docker-compose.yml` 기준 서비스:

- `app` (php-fpm)
- `queue` (queue worker)
- `nginx`
- `mysql`
- `node` (vite dev server)

## 12. 인프라 구축 핵심 (개발환경 중심)

아래는 인프라 문서에서 **개발환경 구축에 직접 필요한 내용만** 압축한 체크리스트입니다.

### 12.1 로컬 Docker 개발환경

목표: 로컬에서 `app + nginx + mysql + node + queue`를 분리 실행

1. 준비
- Docker Desktop 실행
- `docker -v`, `docker compose version` 확인

2. 환경 파일
- `.env_docker` 사용
- 핵심: `APP_ENV=docker`
- DB는 컨테이너 서비스명 기준(`DB_HOST=mysql`)

3. 부트스트랩 분기
- `bootstrap/app.php`에서 Docker 환경일 때 `.env_docker` 로드하도록 구성

4. 컨테이너 경로 규칙
- 프로젝트 마운트 경로를 `/var/www`로 통일
- nginx root는 `/var/www/public`

5. 처음 구성(최초 1회 또는 Dockerfile/compose 변경 시)
```bash
docker compose down -v
docker compose up -d --build
docker compose ps
```

6. 초기화(최초 1회)
```bash
docker exec -it th-app php artisan key:generate --force
docker exec -it th-app php artisan migrate
```

7. 평상시 시작/종료/재시작
```bash
# 시작
docker compose up -d

# 종료
docker compose down

# 재시작
docker compose restart

# 상태 확인
docker compose ps
```

8. 접속 포트(기본 예시)
- Web: `http://localhost:8080`
- Vite: `http://localhost:5173`
- MySQL: `127.0.0.1:3307`

### 12.2 서버 기본 구성(운영/스테이징 공통 뼈대)

운영 서버 기준:
- AWS Lightsail (Ubuntu 22.04 LTS)
- SSH Key 기반 접속

접속 기본 명령(예시):
```bash
# 키 권한(최초 1회)
chmod 400 /path/to/lightsail-key.pem

# Lightsail 서버 접속
ssh -i /path/to/lightsail-key.pem ubuntu@<LIGHTSAIL_STATIC_IP>
```

참고:
- 실제 IP/키 파일명은 문서에 직접 기록하지 않고 로컬/사내 비공개 문서에서 관리합니다.
- 운영 계정은 일반적으로 `ubuntu`를 사용합니다.

권장 최소 흐름:

1. Ubuntu LTS + 고정 IP + 22/80/443 오픈
2. Nginx, PHP-FPM(8.2 계열), MySQL(8 계열), Node/npm 설치
3. 타임존 `Asia/Seoul` 통일
4. 프로젝트 배포 디렉터리 고정(예: `/var/www/th-study`)
5. Nginx 서버블록 + HTTPS(Let’s Encrypt) 적용
6. Queue 워커 상시 실행(systemd 또는 Docker queue 서비스)

프론트 자산 모드 구분:
- 개발: `npm run dev` (HMR/개발용)
- 운영: `npm run build` (정적 빌드 결과 배포)

### 12.3 SSL(운영 필수)

운영 서버는 HTTPS를 기본으로 사용합니다.

1. Certbot 설치
```bash
sudo apt update
sudo apt install -y certbot python3-certbot-nginx
```

2. 인증서 발급 + Nginx 자동 설정
```bash
sudo certbot --nginx -d example.com -d www.example.com
```

3. 자동 갱신 점검
```bash
sudo certbot renew --dry-run
```

참고:
- 도메인 A 레코드가 서버 고정 IP를 가리켜야 발급됩니다.
- 운영 시 80/443 포트가 열려 있어야 합니다.

### 12.4 메모리 안정화(소형 인스턴스 필수)

소형 서버(특히 1~2GB)에서는 스왑 설정이 사실상 필수입니다.

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
free -h
```

추가 권장:
- PHP-FPM 워커 수를 서버 메모리에 맞게 제한
- 로그 파일 주기 정리(디스크 급증 방지)

### 12.5 CI/CD 핵심 (Self-hosted Runner 방식)

이 프로젝트는 서버에서 직접 작업을 수행하는 self-hosted runner 방식이 적합합니다.

핵심 흐름:

1. 서버에 GitHub Actions runner 설치/서비스 등록
2. 서버에서 GitHub 접근 가능한 SSH 인증키 구성
3. 원격 저장소 URL을 SSH 방식으로 사용
4. `main` push 시 배포 워크플로우 실행:
   - 코드 동기화
   - `composer install --no-dev`
   - `php artisan optimize:*`
   - `php artisan migrate --force`
   - 웹서버 reload
   - `php artisan queue:restart`

주의:
- 자동배포 스크립트에 민감정보 직접 하드코딩 금지
- 실제 키/토큰/계정값은 반드시 환경변수 또는 서버 비밀 저장소로 관리

### 12.6 DB 백업/복구 최소 운영안

권장 구조:
- 풀백업(일 1회)
- 증분(binlog, 시간 단위)
- 보관주기(예: 14일) 후 자동삭제

핵심 원칙:
- 백업 계정 분리
- 백업 비밀번호는 별도 권한 파일로 관리
- MySQL 원본 binlog를 수동 삭제하지 않기

복구 기본:
1. 최신 풀백업 복원
2. 해당 시점 이후 binlog 순차 적용

### 12.7 빠른 점검 명령어

```bash
docker compose ps
php artisan about --only=environment,drivers
php artisan migrate:status
php artisan queue:work --once
```

이 4가지만으로도 로컬/서버 기본 동작 여부를 빠르게 확인할 수 있습니다.

### 12.8 운영 경로 기준(최소 공유용)

운영/복구 대응 속도를 위해 아래 경로는 README에 유지합니다.

- 앱 루트: `/var/www/th-study`
- Nginx 로그: `/var/log/nginx/access.log`, `/var/log/nginx/error.log`
- Laravel 로그: `/var/www/th-study/storage/logs`
- DB 풀백업: `/backup/mysql/full`
- DB 증분백업(binlog): `/backup/binlog`

백업 정책(예시):
- 풀백업: 일 1회
- 증분백업: 시간 단위
- 보관주기: 14일

복구 순서(요약):
1. 최신 풀백업 복원
2. 해당 시점 이후 binlog 순차 적용

### 12.9 GitHub 조직관리 가이드

프로젝트를 개인 저장소에서 조직(Organization) 기반으로 운영할 때의 최소 절차입니다.

1. 조직 생성
- GitHub 우상단 프로필 → `Your organizations` → `New organization` → `Free`
- 예시 조직명: `th-study-group`

2. 저장소를 조직으로 이전
- 대상 저장소 → `Settings` → `Danger Zone` → `Transfer ownership`
- 새 Owner를 조직명으로 지정

3. 멤버 초대
- 조직 페이지 → `People` → `Invite member`
- 초대된 사용자는 메일/알림에서 `Join` 완료 필요

4. 팀(Team) 구성(선택)
- 조직 페이지 → `Teams` → `New team`
- 예: Backend / Frontend / Ops
- 팀 단위로 저장소 권한 부여

5. 권한 기준(권장)
- `Read`: 코드 조회/clone
- `Write`: push/PR 작업
- `Admin`: 저장소 설정 관리
- `Owner`: 조직 전체 관리

운영 권장:
- 실서버 계정은 최소권한 원칙(가능하면 `Read`)
- 저장소 설정 변경 권한은 소수 인원만 유지

6. 서버 연동용 토큰(PAT) 발급
- 경로: GitHub `Settings` → `Developer settings` → `Fine-grained tokens`
- 권장 설정:
  - Repository access: `Only select repositories` (대상 저장소만 선택)
  - Permissions: `Contents: Read-only` (서버 pull만 필요 시)
  - 필요 시에만 `Read and write` 부여

중요:
- `Contents` 권한이 없으면 clone/pull 실패(403)
- 토큰은 생성 시 1회만 확인 가능하므로 즉시 안전한 곳에 보관

7. 서버에서 저장소 접근 방식
- HTTPS + PAT 또는 SSH Key 중 하나로 통일
- 자동배포 환경(CI/CD)은 SSH 방식 또는 self-hosted runner 권장

8. 브랜치 보호(권장)
- 저장소 → `Settings` → `Branches` → `Add rule`
- `main` 보호 예시:
  - force push 금지
  - PR 리뷰 후 머지
  - 필요 시 status check 필수화

9. 운영 보안 체크
- PAT/SSH 개인키를 README, 코드, 스크립트에 직접 기록하지 않기
- 배포 토큰 만료 주기 관리
- 퇴사/권한 변경 시 즉시 토큰 폐기 및 팀 권한 정리

## 13. 운영 보안 가이드

- `.env` 및 민감한 키/토큰/패스워드는 저장소에 커밋하지 않습니다.
- README에는 실제 운영 계정, 실제 도메인 내부정보, 비밀키를 기록하지 않습니다.
- 로컬 개발용 라우트(`_dev/*`)는 운영에서 노출되지 않도록 환경 정책을 유지합니다.
- 메일/로그는 운영 추적용으로만 활용하고 개인정보 최소 수집 원칙을 지킵니다.

## 14. 기능 확장 시 체크리스트

기능 추가 시 아래 순서로 보면 소스 탐색이 빠릅니다.

1. `routes/*`에서 엔드포인트 위치 확인
2. 대응 `Controller` 확인
3. `Request` 검증 규칙 확인
4. `Service` 도메인 규칙 확인
5. `Repository`/`Model` DB 처리 확인
6. `Policy`/`Middleware` 접근 정책 확인
7. `views/*` 화면 및 `emails/*` 템플릿 확인

이 순서를 기준으로 보면 기능이 커져도 추적 경로가 안정적으로 유지됩니다.

## 15. 홈/인트로 퍼블리싱 가이드

향후 퍼블리싱 업데이트 시 아래 기준을 유지하면 브랜드 일관성을 지키기 쉽습니다.

### 15.1 페이지 역할 분리

- `home`: 서비스 핵심 요약, 신뢰 요소, CTA, 문의 전환
- `intro`: 섹션 전환 기반 스토리텔링, 약력/철학/확장 방향 강조

### 15.2 콘텐츠 구조 참고

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

### 15.3 스타일 시스템 참고

- `public/css/intro/home.css`
  - 다크 톤 그라디언트 배경 + 강조 색상(`--accent`, `--accent2`)
  - 카드/칩/버튼 컴포넌트 중심 구성
  - `reveal` 클래스로 섹션 등장 연출
- `public/css/intro/intro.css`
  - 풀스크린 섹션 전환, Dot Nav, Hero 배경 패럴랙스
  - 태그/액션 버튼/리스트의 일관된 시각 규칙
  - 모바일/접근성(`prefers-reduced-motion`) 대응 포함

### 15.4 인터랙션 구현 참고

- `public/js/intro/home.js`
  - 앵커 스무스 스크롤
  - `IntersectionObserver` 기반 reveal 애니메이션
- `public/js/intro/intro.js`
  - 섹션 전환(휠/터치/키보드)
  - 타이핑 효과(`나를 뛰어넘는 개발자`)
  - 이미지/배경 패럴랙스
  - 내장 SVG 아이콘 동적 주입

### 15.5 브랜드 이미지/자산 운영 가이드

- 홈 핵심 이미지: `public/images/main_logo.png`, `public/images/extension_logo.png`, `public/images/intro_project_img.jpg`
- 소개 섹션 이미지: `public/images/intro/*.avif`
- 외부 소스 참고: Unsplash(`https://unsplash.com/ko`), Devicon(`https://devicon.dev/`)
- 업데이트 권장 방식
  - 새 이미지 추가 시 기존 명명 규칙(`intro/001.avif` 형태) 유지
  - 텍스트 변경 시 슬로건/키워드/CTA를 함께 조정
  - 섹션 삭제/추가 시 `home.js`, `intro.js`의 앵커/네비게이션 동작 동시 점검
  - 문구는 "성장 + 확장 + 운영" 3축을 유지

## 16. 마무리 한 줄

티에이치스터디그룹은 하나의 사이트가 아니라, 개발자로서의 성장 과정을 담는 플랫폼이다.
