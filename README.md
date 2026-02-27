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
- Infra: Ubuntu(Nginx + PHP-FPM) self-hosted deploy

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

### 슈퍼어드민 시더 정책

- 기준 파일: `database/seeders/DatabaseSeeder.php`
- 운영(`production`)에서는 `AutoSuperAdminSeeder`를 실행해 슈퍼어드민 계정을 보정합니다.
- 개발/로컬에서는 `EnvSuperAdminSeeder`를 실행해 `.env` 기준으로 슈퍼어드민 계정을 보정합니다.
- 두 시더 모두 이메일 기준 `firstOrNew` 패턴으로 동작해, 같은 계정을 중복 생성하지 않고 갱신합니다.
- 비밀번호/토큰/실계정 값은 문서나 저장소에 기록하지 않고 환경변수로만 관리합니다.

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
- 구독이 있으면 `/push/exists`로 서버 존재 여부를 확인하고, 없으면 재등록합니다.
- 구독이 없고 권한이 허용된 경우 `subscribeAndSave()`로 새 구독을 만들고 서버(`/push/subscribe`)에 저장합니다.
- `/push/ping`은 로그인 직후 첫 페이지에서만 최근접속시각 갱신 용도로 1회 호출합니다.

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
- 계정은 디바이스별 다중 구독을 유지합니다(웹/앱/브라우저별 endpoint 공존).
- 로그아웃 시에는 현재 디바이스 endpoint 해제를 우선 시도하고, 다른 디바이스 구독은 유지합니다.
- 회원탈퇴 시에는 해당 사용자의 구독을 서버에서 전체 삭제합니다.

4. 푸시 발송
- 서비스 레이어에서 `PushService::sendToUser()`를 호출합니다.
- 대상 사용자별 `SendWebPushJob`이 큐에 등록되고, Job에서 실제 웹푸시를 전송합니다.
- 발송 이력은 `web_push_messages`에 기록되며, `success_flag(1/0)`와 `send_error_message(JSON)`에 전송 결과를 남깁니다.

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
- `web_push_subscriptions`: 디바이스별 웹 푸시 구독
- `web_push_messages`: 푸시 발송/클릭/성공여부 이력
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

# 슈퍼어드민 보정(환경별 택1)
docker exec -it th-app php artisan db:seed --class=EnvSuperAdminSeeder --force
# docker exec -it th-app php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
docker exec -it th-app php artisan db:seed --class=NoteMasterSeeder --force
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
6. Queue 워커 상시 실행(systemd 서비스)

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
4. `main` push 시 `.github/workflows/deploy.yml` 배포 워크플로우 실행:
   - 코드 동기화
   - `composer install --no-dev`
   - `php artisan optimize:clear`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
   - `php artisan migrate --force`
   - `php artisan db:seed --class=NoteMasterSeeder --force` (노트 마스터 동기화)
   - 웹서버 reload (`php8.2-fpm`, `nginx`)
   - `php artisan queue:restart`

수동 실행 명령어(슈퍼어드민/노트 마스터):

```bash
# 개발/로컬: .env(SUPERADMIN_EMAIL, SUPERADMIN_PASSWORD) 기준 슈퍼어드민 보정
php artisan db:seed --class=EnvSuperAdminSeeder --force

# 운영: SUPERADMIN_EMAIL 기준 슈퍼어드민 보정(비밀번호 랜덤 생성, 콘솔 출력)야
php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
php artisan db:seed --class=NoteMasterSeeder --force
```

노트 마스터 시더 운영 규칙:
- 시더 데이터는 `config/seeders/note.php`에서 관리
- 실행 방식은 `updateOrCreate` 기반으로 중복 insert 없이 upsert 형태 동기화
- `groups`, `categories`, `topics`를 코드 기반(`group_code`, `category_code`)으로 연결

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
php artisan about --only=environment,drivers
php artisan migrate:status
sudo systemctl status php8.2-fpm
sudo systemctl status nginx
sudo systemctl status th-study-queue
```

이 명령들로 서버 기본 동작 여부를 빠르게 확인할 수 있습니다.

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

## 17. 블로그 서비스 개발 작업 기록 (2026-02-26 기준)

아래 내용은 블로그 서비스 기능 관련으로 채팅 작업을 통해 반영된 실제 변경 이력입니다.

### 17.1 화면/라우팅/폼 처리

- 블로그 글 작성 화면: `resources/views/blogs/create.blade.php`
- 저장 action은 컨트롤러에서 전달한 `formAction`만 사용하도록 고정
- 저장 버튼 클릭 시 프론트 검증 추가
  - 제목 미입력: 알림 + 제목 포커스
  - 주제 미선택: 알림 + 주제 포커스
  - 내용 미입력: 알림 + 에디터 포커스
  - 정상 입력: `#form-note` submit
- 상단 에러 UI를 `inquiries/create`와 동일한 `alert alert-warning + badge` 형태로 통일
- 필드별 백엔드 에러 출력 영역 추가
  - `subject`, `topic`, `content`, `thumbnail_path`, `tags`
  - `is-invalid`, `@error`, `old()` 적용

### 17.2 주제(Topic) 조회 구조 개선

- 구조: Controller -> Service -> Repository
- 추가 파일
  - `app/Repositories/NoteTopicRepository.php`
  - `app/Services/NoteService.php`
- `with(['category.group'])` 관계 조회 적용
- 조회 조건
  - `use_flag = 1`
  - 라우트 `group/slug`와 일치하는 group/category만
- 그룹 코드 매핑 처리
  - URL 그룹(`blogs`) -> DB 그룹코드(`blog`)
  - `config('note.group')` 기반으로 변환 후 조회

### 17.3 권한/검증(FormRequest + Policy)

- FormRequest 추가: `app/Http/Requests/Notes/StoreNoteRequest.php`
- Policy 추가: `app/Policies/NotePolicy.php`
  - 조회는 허용
  - 작성/수정/삭제/공개여부 변경은 `user.level = admin`만 허용
- Policy 등록: `app/Providers/AuthServiceProvider.php`

검증 규칙(저장 전):
- `subject`: required|string|min:5|max:100
- `topic`: required|integer|exists(note_topics.idx, use_flag=1, delete_datetime null)
- `content`: required|string|min:10
- `thumbnail_path`: nullable|image|mimes:jpg,jpeg,png|max:51200(50MB)
- `tags`: nullable|string
  - 최대 10개
  - 각 태그 최대 20자
- 추가 after 검증
  - 선택한 topic이 현재 group/category에 실제 속하는지 검증

검증 메시지 관리:
- FormRequest 내부 `messages()` 제거
- `resources/lang/ko/validation.php`의 `custom`/`attributes`로 이전

### 17.4 블로그 글 저장/이동 흐름

- 컨트롤러 `store()`는 정상 입력 시 `show`로 이동
  - `to_route("{$group}.show", ['slug' => $slug, 'idx' => $note->idx])`
- 성공 안내 문구("입력값 검증...") 제거
- 라우트 default 의존 제거
  - `group`은 라우트명(`blogs.store`)에서 파싱해 사용

### 17.5 썸네일 업로드 처리

- 구현 위치: `app/Services/NoteService.php`
- 라이브러리: `intervention/image` v3
- 처리 규칙
  - EXIF 회전 보정(`orient`)
  - 가로 1600px 초과 시 축소(`scaleDown(width: 1600)`)
  - PNG는 PNG 유지
  - JPEG 계열은 JPG(quality 80)
- 저장 위치/규칙
  - disk: `public`
  - 경로: `storage/app/public/{YYYYMM}/{uuid}.{ext}`
- 업로드 필드명 통일: `thumbnail_path`

### 17.6 해시태그 저장 처리

- 프론트
  - 파일: `public/js/blog.js`
  - 태그 관리 함수 분리(전역 태그 배열 제거)
  - 입력 규칙: Enter/`,`로 추가, 중복 방지, 최대 10개
  - 한글 조합 입력 대응(composition 이벤트)
  - hidden `tags`에 콤마 문자열 동기화
- 백엔드
  - 노트 생성 시 태그 파싱 -> 저장
  - 추가 파일
    - `app/Repositories/NoteTagRepository.php`
    - `app/Repositories/NoteTagMapRepository.php`
  - 처리
    - `note_tags`: 이름 기준 조회/생성(soft delete 복구 포함)
    - `note_tag_map`: `insertOrIgnore`로 매핑 저장

### 17.7 히스토리(Event 기반) 적용

- 이벤트 추가: `app/Events/NoteHistoryEvent.php`
- 리스너 추가: `app/Listeners/WriteNoteHistoryEventListener.php`
- 이벤트 등록: `app/Providers/EventServiceProvider.php`
- 노트 생성 시 `job_type = 등록` 기록
  - 기록 필드: `note_idx`, `job_type`, `ip`, `user_agent`, `referer_url`, `create_user_idx`
- 불일치 케이스 로그 추가
  - topic/group/category 불일치 시 warning 로그 남김

### 17.8 업로드 실패 원인 분석 및 php.ini 조정

증상:
- 로그: `thumbnail_path => 업로드에 실패했습니다.(uploaded)`

원인:
- 로컬 PHP 업로드 제한이 낮음
  - `upload_max_filesize = 2M`
  - `post_max_size = 8M`

조치:
- 로컬 Homebrew PHP 설정 파일 수정
  - 파일: `/opt/homebrew/etc/php/8.2/php.ini`
  - 변경:
    - `upload_max_filesize = 50M`
    - `post_max_size = 50M`
- 반영 확인: `php -i`에서 50M 확인

참고:
- Docker 설정(`docker/php/conf.d/zz-custom.ini`)은 이미 50M 상태
- 실사용 서버 종류에 따라 PHP-FPM/웹서버 재시작 필요

### 17.9 관련 핵심 파일 목록

- 컨트롤러: `app/Http/Controllers/NoteController.php`
- 요청검증: `app/Http/Requests/Notes/StoreNoteRequest.php`
- 서비스: `app/Services/NoteService.php`
- 레퍼지토리:
  - `app/Repositories/NoteRepository.php`
  - `app/Repositories/NoteTopicRepository.php`
  - `app/Repositories/NoteTagRepository.php`
  - `app/Repositories/NoteTagMapRepository.php`
- 권한: `app/Policies/NotePolicy.php`
- 이벤트/리스너:
  - `app/Events/NoteHistoryEvent.php`
  - `app/Listeners/WriteNoteHistoryEventListener.php`
- 라우트: `routes/content.php`
- 화면/스크립트:
  - `resources/views/blogs/create.blade.php`
  - `resources/views/blogs/show.blade.php`
  - `public/js/blog.js`
  - `public/js/toast_ui_editor.js`
  - `public/css/blog.css`
- 언어 메시지: `resources/lang/ko/validation.php`

### 17.10 content 저장/출력 전환 (Markdown -> HTML)

- 에디터 동기화 전환
  - `public/js/toast_ui_editor.js`
  - `editor.getMarkdown()` -> `editor.getHTML()`로 변경
- 저장 정책
  - 블로그 글 저장 전 `content`를 서버에서 sanitize 후 DB 저장
  - 허용 태그 중심(`p, br, strong, b, em, i, u, s, h1~h6, ul, ol, li, blockquote, pre, code, a`)
  - 위험 태그/속성 제거(`script/style/iframe/object/embed`, `on*` 이벤트 속성)
  - `a[href]`의 `javascript:`/`data:` 차단
- 상세 출력 정책(기존 데이터 호환)
  - `content`가 HTML 패턴이면 sanitize 후 그대로 렌더
  - 기존 Markdown 패턴이면 `Str::markdown(..., safe 옵션)` 변환 후 렌더
  - 최종 출력은 `resources/views/blogs/show.blade.php`에서 `{!! $contentHtml !!}` 단일 경로 사용
- 입력 검증 보강
  - HTML 태그 제거 후 순수 텍스트 길이 기준 10자 이상 검증
  - `<p><br></p>` 같은 의미 없는 입력 통과 방지

## 18. 블로그 목록/상세 고도화 작업 기록 (2026-02-27 기준)

### 18.1 목록 조회 구조(초기 진입 + AJAX 공용화)

- 목록 조회 로직을 서비스 단에서 공용화하고, 컨트롤러는 화면 진입과 AJAX 응답이 같은 데이터 빌더를 공유하도록 정리
- 첫 진입은 SSR 기준으로 렌더링하고, 이후 더보기/검색은 AJAX로 동일 응답 구조를 재사용
- 기본 페이지 크기는 10건으로 고정, 버튼 기반 스크롤 페이징에서 10건씩 추가 로드
- 목록 AJAX 응답에 `items`, `pagination`, `filters`를 포함해 프론트 상태 동기화 단순화

### 18.2 목록 검색 검증(FormRequest)

- 목록 검색 쿼리 검증을 FormRequest로 이관
- 적용 파일: `app/Http/Requests/Notes/IndexNoteRequest.php`
- 검증 대상: `search_select_type`, `search_keyword`, `page`
- 컨트롤러 `index()`는 `IndexNoteRequest` 기반으로만 목록 처리

### 18.3 라우팅/카테고리 정책 정비

- `/blogs` 접근 시 블로그 그룹 전체 카테고리 글 조회
- `/blogs/{slug}` 접근 시 해당 카테고리 글만 조회
- slug가 실제 카테고리 코드와 불일치하면 404 처리
- 목록 타이틀은 카테고리별 동적 표기(`개발 글`, `여행 글`), 전체는 `전체 글`

### 18.4 목록 카드 UX 개선

- 목록 시간 표기를 절대시각에서 상대시간으로 변경(예: `3분 전`, `1시간 전`)
- 썸네일 미등록 글은 기본 이미지(`public/images/no_image.png`) 노출
- 카드 내 `더보기`는 링크(`<a>`) 대신 버튼 + 스크립트 이동으로 통일
- 모바일/PC에서 시간과 더보기 버튼 라인 정렬 보정

### 18.5 목록 팝업 상세(AJAX)

- 목록 아이템 클릭 시 팝업 상세를 AJAX로 조회
- 목록 조회 API는 목록 데이터만 반환하고, 상세는 별도 상세 API 호출로 분리
- 팝업 열림 시 배경 블러/오버레이가 헤더 포함 전체 영역에 적용되도록 z-index 및 DOM 위치 조정
- 공통 AJAX 모듈(`requestAjax`) 사용으로 로딩바 노출 일관성 확보(목록 조회/더보기/상세/삭제/공개설정)

### 18.6 팝업 액션 정책

- 수정: 상세 페이지(수정 폼)로 이동
- 삭제: 팝업 내 confirm 후 AJAX soft delete 처리, 성공 시 목록 카드/카운트 즉시 갱신
- 공개설정: 팝업에서 즉시 토글 처리(AJAX)
- 권한/상태에 따라 수정/삭제 버튼 표시 조건을 동적으로 재계산

### 18.7 권한/노출 정책 보강

- 카테고리 화면에서는 admin 권한이 아닌 경우 상단/하단 작성하기 버튼 비노출
- 공개상태 변경 직후 목록 팝업 액션 버튼 상태(수정/삭제)가 즉시 반영되도록 클라이언트 상태 갱신

### 18.8 상세 페이지 메타/OG 상속 구조

- 레이아웃 메타 태그를 `@yield(..., 기본값)` 구조로 변경해 페이지별 override 지원
- 적용 파일: `resources/views/layouts/app.blade.php`
- 상세 페이지(`resources/views/blogs/show.blade.php`)는 제목/설명/이미지를 게시글 기준으로 override
- `og:image`는 썸네일 존재 시 썸네일 URL, 없으면 기본 OG 이미지(`images/og/001.png`)

### 18.9 운영 로그 보강

- 블로그 서비스에서 목록 조회/단건 조회 로그 추가(`Log::info`)
- 로그 필수 필드로 `ip` 포함 여부 점검 및 일관성 유지
- 히스토리 테이블 기록과 별개로 운영 추적 로그를 동일 패턴으로 보강

### 18.10 관련 핵심 파일

- 컨트롤러: `app/Http/Controllers/NoteController.php`
- 서비스: `app/Services/NoteService.php`
- 요청검증: `app/Http/Requests/Notes/IndexNoteRequest.php`
- 레포지토리: `app/Repositories/NoteRepository.php`
- 목록 화면: `resources/views/blogs/index.blade.php`
- 상세 화면: `resources/views/blogs/show.blade.php`
- 레이아웃: `resources/views/layouts/app.blade.php`
- 스크립트: `public/js/blog.js`
- 스타일: `public/css/blog.css`
