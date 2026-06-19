# Database Schema

## 1. 기준

- 기준 파일
  - `config/database.php`
  - `database/migrations/*`
- 기본 연결
  - `mysql`
- MySQL prefix
  - `th_`
- 아래 실제 테이블명은 `mysql` 연결 기준으로 `th_` prefix가 반영된 이름으로 정리
- 모델명 기준 추정은 제외하고, 마이그레이션 최종 상태 기준으로 작성
- 명시적 FK 제약은 많지 않으므로, 관계는 컬럼명/주석 기준의 논리 관계로 해석

## 2. 요약

### 2.1 빠른 보기

- 사용자/인증: 5개 테이블
  - 회원, 세션, 비밀번호 재설정, 로그인/메일 로그
- 게시판: 4개 테이블
  - 게시글, 댓글, 비회원 문의, 게시글 이력
- 노트: 7개 테이블
  - 그룹, 카테고리, 주제, 본문, 태그, 매핑, 이력
- 푸시: 2개 테이블
  - 구독 정보, 발송/클릭 이력
- 유입/전환: 4개 테이블
  - 사용자 유입, 봇 유입, 전환 로그, 일별 집계
- 큐: 2개 테이블
  - `jobs`, `failed_jobs`

### 2.2 사용자 / 인증

- `users` -> `th_users`
  - PK: `idx`
  - 용도: 회원 정보, 인증 상태, 세션/접속 제어
- `password_reset_tokens` -> `th_password_reset_tokens`
  - PK: `email`
  - 용도: 비밀번호 재설정 토큰
- `sessions` -> `th_sessions`
  - PK: `id`
  - 용도: 세션 저장
- `login_logs` -> `th_login_logs`
  - PK: `idx`
  - 용도: 로그인 시도 이력
- `mail_logs` -> `th_mail_logs`
  - PK: `idx`
  - 용도: 메일 발송/수신 이력

### 2.3 게시판

- `posts` -> `th_posts`
  - PK: `idx`
  - 용도: 공지/문의 등 게시글
- `comments` -> `th_comments`
  - PK: `idx`
  - 용도: 게시글 댓글
- `guest_posts` -> `th_guest_posts`
  - PK: `idx`
  - 용도: 비회원 문의
- `post_histories` -> `th_post_histories`
  - PK: `idx`
  - 용도: 게시글 작업 이력

### 2.4 노트

- `note_groups` -> `th_note_groups`
  - PK: `idx`
  - 용도: 노트 그룹 마스터
- `note_categories` -> `th_note_categories`
  - PK: `idx`
  - 용도: 노트 카테고리 마스터
- `note_topics` -> `th_note_topics`
  - PK: `idx`
  - 용도: 노트 주제 마스터
- `notes` -> `th_notes`
  - PK: `idx`
  - 용도: 노트 본문
- `note_tags` -> `th_note_tags`
  - PK: `idx`
  - 용도: 노트 해시태그 마스터
- `note_tag_map` -> `th_note_tag_map`
  - PK: `note_idx + tag_idx`
  - 용도: 노트-태그 매핑
- `note_histories` -> `th_note_histories`
  - PK: `idx`
  - 용도: 노트 작업 이력

### 2.5 푸시

- `web_push_subscriptions` -> `th_web_push_subscriptions`
  - PK: `idx`
  - 용도: 웹푸시 구독 정보
- `web_push_messages` -> `th_web_push_messages`
  - PK: `idx`
  - 용도: 웹푸시 발송/클릭 이력

### 2.6 유입 / 전환 / 통계

- `access_logs` -> `th_access_logs`
  - PK: `idx`
  - 용도: 사용자 유입 raw 로그
- `bot_access_logs` -> `th_bot_access_logs`
  - PK: `idx`
  - 용도: 봇 유입 raw 로그
- `daily_page_stats` -> `th_daily_page_stats`
  - PK: `stat_date + access_page + device_type`
  - 용도: 일별 페이지 집계
- `conversion_logs` -> `th_conversion_logs`
  - PK: `id`
  - 용도: 전환 이벤트 raw 로그

### 2.7 큐

- `jobs` -> `th_jobs`
  - PK: `id`
  - 용도: database queue 작업
- `failed_jobs` -> `th_failed_jobs`
  - PK: `id`
  - 용도: 실패한 queue 작업

## 3. 관계 요약

| 컬럼 | 참조 대상 | 비고 |
| --- | --- | --- |
| `posts.user_idx` | `th_users.idx` | 작성 사용자 |
| `comments.user_idx` | `th_users.idx` | 댓글 작성 사용자 |
| `comments.post_idx` | `th_posts.idx` | 대상 게시글 |
| `guest_posts.update_user_idx` | `th_users.idx` | 관리자 수정자 |
| `guest_posts.delete_user_idx` | `th_users.idx` | 관리자 삭제자 |
| `post_histories.post_idx` | `th_posts.idx` | 대상 게시글 |
| `post_histories.create_user_idx` | `th_users.idx` | 작업 사용자 |
| `note_categories.group_idx` | `th_note_groups.idx` | 그룹 참조 |
| `note_topics.categories_idx` | `th_note_categories.idx` | 카테고리 참조 |
| `notes.group_idx` | `th_note_groups.idx` | 그룹 참조 |
| `notes.categories_idx` | `th_note_categories.idx` | 카테고리 참조 |
| `notes.topic_idx` | `th_note_topics.idx` | 주제 참조 |
| `note_tag_map.note_idx` | `th_notes.idx` | 노트 참조 |
| `note_tag_map.tag_idx` | `th_note_tags.idx` | 태그 참조 |
| `note_histories.note_idx` | `th_notes.idx` | 대상 노트 |
| `web_push_subscriptions.user_idx` | `th_users.idx` | 사용자 참조 |
| `web_push_messages.user_idx` | `th_users.idx` | 사용자 참조 |
| `access_logs.user_idx` | `th_users.idx` | 로그인 사용자 기준 |
| `conversion_logs.user_idx` | `th_users.idx` | 로그인 사용자 기준 |

## 4. 테이블 상세

### 4.1 `users` -> `th_users`

- 용도
  - 회원 정보, 인증 상태, 접속 상태, 세션 버전 관리
- PK
  - `idx`
- 주요 컬럼
  - `email`: 이메일, unique
  - `name`: 사용자명, index
  - `password`: 패스워드
  - `nick_name`: 사용자 닉네임, unique
  - `level`: 회원등급, 기본값 `nomal`
  - `email_verify_token`: 이메일 인증 코드
  - `session_version`: 개인정보 변경 시 로그아웃 강제화
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `email`: `string(80)`, not null, unique
  - `name`: `string(30)`, not null, index
  - `password`: `string(255)`, not null
  - `nick_name`: `string(30)`, not null, unique
  - `birth_date`: `date`, not null, index
  - `sex`: `char(1)`, not null
  - `phone`: `string(11)`, not null, index
  - `address`: `string(150)`, null, default `null`, index
  - `personal_info_agree`: `unsignedTinyInteger`, not null, default `0`
  - `marketing_info_agree`: `unsignedTinyInteger`, not null, default `0`
  - `push_notification_agree`: `unsignedTinyInteger`, not null, default `0`
  - `memo`: `text`, null, default `null`
  - `level`: `string(20)`, null, default `nomal`
  - `ip`: `ipAddress`, null, default `null`
  - `last_access_datetime`: `dateTime`, null, default `null`
  - `email_verify_token`: `string(6)`, null, default `null`
  - `email_verify_datetime`: `dateTime`, null, default `null`
  - `email_verify_exp_datetime`: `dateTime`, null, default `null`
  - `change_password_flag`: `tinyInteger`, not null, default `0`
  - `remember_token`: `rememberToken`, null, default `null`
  - `session_version`: `unsignedInteger`, not null, default `1`
  - `create_user_idx`: `unsignedBigInteger`, null, default `null`
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.2 `password_reset_tokens` -> `th_password_reset_tokens`

- 용도
  - 비밀번호 재설정 토큰 저장
- PK
  - `email`
- 주요 컬럼
  - `token`: 재설정 토큰
  - `created_at`: 생성 시각
- 전체 컬럼
  - `email`: `string`, not null, primary key
  - `token`: `string`, not null
  - `created_at`: `timestamp`, null, default `null`

### 4.3 `sessions` -> `th_sessions`

- 용도
  - Laravel 세션 저장
- PK
  - `id`
- 주요 컬럼
  - `user_id`: 사용자 ID, index
  - `payload`: 세션 데이터
  - `last_activity`: 마지막 활동 시각, index
- 전체 컬럼
  - `id`: `string`, not null, primary key
  - `user_id`: `foreignId`, null, default `null`, index
  - `ip_address`: `string(45)`, null, default `null`
  - `user_agent`: `text`, null, default `null`
  - `payload`: `longText`, not null
  - `last_activity`: `integer`, not null, index

### 4.4 `login_logs` -> `th_login_logs`

- 용도
  - 로그인 시도 로그 저장
- PK
  - `idx`
- 주요 컬럼
  - `email`: 접속 이메일 계정, index
  - `ip`: 접속 아이피, index
  - `login_provider`: 로그인 방식, 기본값 `local`
  - `success_flag`: 1 성공 / 0 실패
- 전체 컬럼
  - `idx`: `unsignedBigInteger(true)`, not null, default auto increment
  - `email`: `string(80)`, not null, index
  - `ip`: `ipAddress`, not null, index
  - `login_provider`: `string(20)`, not null, default `local`
  - `access_datetime`: `dateTime`, not null, index
  - `access_user_idx`: `unsignedBigInteger`, null, default `null`, index
  - `user_agent`: `string(512)`, not null
  - `success_flag`: `unsignedTinyInteger`, not null

### 4.5 `mail_logs` -> `th_mail_logs`

- 용도
  - 메일 발송/수신 이력 저장
- PK
  - `idx`
- 주요 컬럼
  - `email`: 수신 이메일, index
  - `kind`: 메일 종류, index
  - `token`: 이메일 인증/링크 토큰, index
  - `send_datetime`: 메일 발송시각, index
- 전체 컬럼
  - `idx`: `unsignedBigInteger(true)`, not null, default auto increment
  - `email`: `string(80)`, not null, index
  - `kind`: `string(20)`, not null, index
  - `token`: `string(255)`, null, default `null`, index
  - `send_datetime`: `dateTime`, not null, index
  - `sender`: `unsignedBigInteger`, null, default `null`
  - `receive_datetime`: `dateTime`, null, default `null`
  - `receive_ip`: `ipAddress`, null, default `null`

### 4.6 `posts` -> `th_posts`

- 용도
  - 공지/문의 등 게시글 공통 저장
- PK
  - `idx`
- 주요 컬럼
  - `user_idx`: 작성 사용자, index
  - `title`: 글 제목, index
  - `status`: 진행상태, 기본값 `wait`, index
  - `post_type`: 게시판 유형, null 가능, index
  - `use_flag`: 공개여부, 기본값 `1`
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `user_idx`: `unsignedBigInteger`, not null, index
  - `title`: `string(255)`, not null, index
  - `content`: `longText`, not null
  - `status`: `string(15)`, not null, default `wait`, index
  - `post_type`: `string(20)`, null, default `null`, index
  - `use_flag`: `tinyInteger`, not null, default `1`
  - `create_user_idx`: `unsignedBigInteger`, not null
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.7 `comments` -> `th_comments`

- 용도
  - 게시글 댓글 저장
- PK
  - `idx`
- 주요 컬럼
  - `user_idx`: 댓글 작성 사용자
  - `post_idx`: 대상 게시글
  - `content`: 댓글내용
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `user_idx`: `unsignedBigInteger`, not null
  - `post_idx`: `unsignedBigInteger`, not null
  - `content`: `mediumText`, not null
  - `create_user_idx`: `unsignedBigInteger`, not null
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.8 `guest_posts` -> `th_guest_posts`

- 용도
  - 비회원 문의/게시글 저장
- PK
  - `idx`
- 주요 컬럼
  - `status`: 진행상태, 기본값 `wait`, index
  - `post_type`: 게시판 유형, null 가능, index
  - `contact_method`: 연락수단, index
  - `contact_value`: 연락수단 값, index
  - `referer_url`: 접속 referer 원문 URL
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `title`: `string(255)`, not null, index
  - `content`: `longText`, not null
  - `status`: `string(15)`, not null, default `wait`, index
  - `post_type`: `string(20)`, null, default `null`, index
  - `personal_info_agree`: `unsignedTinyInteger`, not null, default `1`
  - `marketing_info_agree`: `unsignedTinyInteger`, not null, default `0`
  - `contact_method`: `string(20)`, null, default `null`, index
  - `contact_value`: `string(50)`, null, default `null`, index
  - `memo`: `text`, null, default `null`
  - `ip`: `ipAddress`, not null
  - `user_agent`: `string(512)`, not null
  - `referer_url`: `string(2048)`, null, default `null`
  - `writer`: `string(30)`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.9 `post_histories` -> `th_post_histories`

- 용도
  - 게시글 작업 이력 저장
- PK
  - `idx`
- 주요 컬럼
  - `post_idx`: 대상 게시글, index
  - `job_type`: 작업구분, index
  - `table_name`: 테이블 유형, index
  - `referer_url`: 접속 referer 원문 URL
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `post_idx`: `unsignedBigInteger`, not null, index
  - `ip`: `ipAddress`, not null
  - `user_agent`: `string(512)`, not null
  - `referer_url`: `string(2048)`, null, default `null`
  - `job_type`: `string(20)`, not null, index
  - `table_name`: `string(64)`, null, default `null`, index
  - `status`: `string(15)`, null, default `null`
  - `post_type`: `string(20)`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `create_user_idx`: `unsignedBigInteger`, null, default `null`

### 4.10 `note_groups` -> `th_note_groups`

- 용도
  - 노트 그룹 마스터
- PK
  - `idx`
- 주요 컬럼
  - `code`: 그룹코드, index
  - `name`: 그룹명, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `code`: `string(20)`, not null, index
  - `name`: `string(30)`, not null, index
  - `create_user_idx`: `unsignedBigInteger`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.11 `note_categories` -> `th_note_categories`

- 용도
  - 노트 카테고리 마스터
- PK
  - `idx`
- 주요 컬럼
  - `group_idx`: 그룹 참조, index
  - `code`: 카테고리 코드, index
  - `name`: 카테고리명, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `group_idx`: `unsignedBigInteger`, not null, index
  - `code`: `string(20)`, not null, index
  - `name`: `string(30)`, not null, index
  - `memo`: `string(255)`, not null
  - `create_user_idx`: `unsignedBigInteger`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.12 `note_topics` -> `th_note_topics`

- 용도
  - 노트 주제 마스터
- PK
  - `idx`
- 주요 컬럼
  - `categories_idx`: 카테고리 참조, index
  - `name`: 주제명, index
  - `use_flag`: 사용여부, 기본값 `0`
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `categories_idx`: `unsignedBigInteger`, not null, index
  - `name`: `string(30)`, not null, index
  - `memo`: `string(255)`, not null
  - `use_flag`: `tinyInteger`, not null, default `0`
  - `create_user_idx`: `unsignedBigInteger`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.13 `notes` -> `th_notes`

- 용도
  - 노트 본문 저장
- PK
  - `idx`
- 주요 컬럼
  - `group_idx`: 그룹 참조, index
  - `categories_idx`: 카테고리 참조, index
  - `topic_idx`: 주제 참조
  - `subject`: 제목, index
  - `thumbnail_path`: 썸네일 경로, null 가능
  - `use_flag`: 공개여부, 기본값 `0`
  - `access_page`: 유입통계 매칭용 접근 경로, null 가능, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `group_idx`: `unsignedBigInteger`, not null, index
  - `categories_idx`: `unsignedBigInteger`, not null, index
  - `topic_idx`: `unsignedBigInteger`, not null
  - `group_code`: `string(20)`, not null, index
  - `categories_code`: `string(20)`, not null, index
  - `subject`: `string(100)`, not null, index
  - `content`: `longText`, not null
  - `thumbnail_path`: `string(500)`, null, default `null`
  - `use_flag`: `tinyInteger`, not null, default `0`
  - `access_page`: `string(255)`, null, default `null`, index
  - `create_user_idx`: `unsignedBigInteger`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.14 `note_tags` -> `th_note_tags`

- 용도
  - 노트 해시태그 마스터
- PK
  - `idx`
- 주요 컬럼
  - `name`: 해시태그명, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `name`: `string(20)`, not null, index
  - `create_user_idx`: `unsignedBigInteger`, not null, index
  - `update_user_idx`: `unsignedBigInteger`, null, default `null`
  - `delete_user_idx`: `unsignedBigInteger`, null, default `null`
  - `create_datetime`: `dateTime`, not null, index
  - `update_datetime`: `dateTime`, null, default `null`
  - `delete_datetime`: `dateTime`, null, default `null`

### 4.15 `note_tag_map` -> `th_note_tag_map`

- 용도
  - 노트-태그 매핑
- PK
  - `note_idx + tag_idx`
- 주요 컬럼
  - `note_idx`: 노트 참조
  - `tag_idx`: 태그 참조, index
- 전체 컬럼
  - `note_idx`: `unsignedBigInteger`, not null, primary key
  - `tag_idx`: `unsignedBigInteger`, not null, primary key, index

### 4.16 `note_histories` -> `th_note_histories`

- 용도
  - 노트 작업 이력 저장
- PK
  - `idx`
- 주요 컬럼
  - `note_idx`: 대상 노트, index
  - `job_type`: 작업구분, index
  - `ip`: 아이피, index
  - `referer_url`: 접속 referer 원문 URL
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `note_idx`: `unsignedBigInteger`, not null, index
  - `job_type`: `string(20)`, not null, index
  - `ip`: `ipAddress`, not null, index
  - `user_agent`: `string(512)`, not null
  - `referer_url`: `string(2048)`, null, default `null`
  - `create_user_idx`: `unsignedBigInteger`, null, default `null`, index
  - `create_datetime`: `dateTime`, not null, index

### 4.17 `web_push_subscriptions` -> `th_web_push_subscriptions`

- 용도
  - 웹푸시 구독 정보 저장
- PK
  - `idx`
- 주요 컬럼
  - `user_idx`: 사용자 참조, index
  - `endpoint`: 디바이스 정보, unique
  - `last_seen_datetime`: 최근접속시간, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `user_idx`: `unsignedBigInteger`, not null, index
  - `endpoint`: `string(500)`, not null, unique
  - `p256dh`: `string(100)`, not null
  - `auth`: `string(100)`, not null
  - `user_agent`: `string(512)`, not null
  - `last_seen_datetime`: `dateTime`, null, default `null`, index
  - `create_datetime`: `dateTime`, not null

### 4.18 `web_push_messages` -> `th_web_push_messages`

- 용도
  - 웹푸시 발송/클릭 이력 저장
- PK
  - `idx`
- 주요 컬럼
  - `user_idx`: 사용자 idx, index
  - `title`: 푸시 제목, index
  - `click_token`: 클릭 추적 토큰
  - `success_flag`: 푸시 성공여부, null 가능
  - `send_error_message`: 실패 상세 JSON, null 가능
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `user_idx`: `unsignedBigInteger`, not null, index
  - `endpoint`: `string(500)`, not null
  - `title`: `string(200)`, not null, index
  - `body`: `text`, not null
  - `click_token`: `string(128)`, not null
  - `target_url`: `string(1024)`, not null
  - `success_flag`: `unsignedTinyInteger`, null, default `null`
  - `send_error_message`: `json`, null, default `null`
  - `user_agent`: `string(512)`, not null
  - `table_name`: `string(64)`, not null
  - `send_datetime`: `dateTime`, not null, index
  - `click_datetime`: `dateTime`, null, default `null`

### 4.19 `access_logs` -> `th_access_logs`

- 용도
  - 사용자 유입 raw 로그 저장
- PK
  - `idx`
- 주요 컬럼
  - `access_date`: 접속일, 복합 인덱스
  - `access_page`: 접속페이지, 복합 인덱스
  - `device_type`: 디바이스 정보, 복합 인덱스 + 단일 인덱스
  - `referer_host`: 접속 도메인, index
  - `session_id`: 세션 ID, index
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `access_date`: `date`, not null, composite index
  - `access_datetime`: `dateTime`, not null
  - `access_page`: `string(255)`, not null, composite index, index
  - `referer_host`: `string(255)`, not null, index
  - `device_type`: `string(20)`, not null, composite index, index
  - `device_brand`: `string(50)`, null, default `null`
  - `device_model`: `string(100)`, null, default `null`
  - `os`: `string(50)`, null, default `null`
  - `browser`: `string(50)`, null, default `null`
  - `ip`: `ipAddress`, not null, index
  - `referer_url`: `string(2048)`, null, default `null`
  - `user_agent`: `string(512)`, not null
  - `session_id`: `string(120)`, null, default `null`, index
  - `user_idx`: `unsignedBigInteger`, null, default `null`, index

### 4.20 `bot_access_logs` -> `th_bot_access_logs`

- 용도
  - 봇 유입 raw 로그 저장
- PK
  - `idx`
- 주요 컬럼
  - `access_date`: 접속일, 복합 인덱스
  - `access_page`: 접속페이지, 복합 인덱스
  - `bot_name`: 봇 명칭, 복합 인덱스
- 전체 컬럼
  - `idx`: `bigIncrements`, not null, default auto increment
  - `access_date`: `date`, not null, composite index
  - `access_datetime`: `dateTime`, not null
  - `access_page`: `string(255)`, not null, composite index, index
  - `referer_host`: `string(255)`, not null, index
  - `bot_name`: `string(100)`, null, default `null`, composite index
  - `referer_url`: `string(2048)`, null, default `null`
  - `user_agent`: `string(512)`, not null

### 4.21 `daily_page_stats` -> `th_daily_page_stats`

- 용도
  - 일별 페이지 통계 집계 저장
- PK
  - `stat_date + access_page + device_type`
- 주요 컬럼
  - `total_access_count`: 총 접속자 수, 기본값 `0`
  - `real_access_count`: 중복 제외 접속자 수, 기본값 `0`
  - `conversion_count`: 전환 수, 기본값 `0`
- 전체 컬럼
  - `stat_date`: `date`, not null, primary key
  - `access_page`: `string(255)`, not null, primary key, index
  - `device_type`: `string(20)`, not null, primary key
  - `total_access_count`: `unsignedInteger`, not null, default `0`
  - `real_access_count`: `unsignedInteger`, not null, default `0`
  - `conversion_count`: `unsignedInteger`, not null, default `0`
  - `create_datetime`: `dateTime`, not null
  - `update_datetime`: `dateTime`, null, default `null`

### 4.22 `conversion_logs` -> `th_conversion_logs`

- 용도
  - 전환 이벤트 raw 로그 저장
- PK
  - `id`
- 주요 컬럼
  - `conversion_date`: 전환일자, 복합 인덱스
  - `conversion_type`: 전환유형, index
  - `target_page`: 전환 후 이동 페이지
  - `device_type`: 디바이스 정보, 복합 인덱스
  - `session_id`: 세션 ID, index
- 전체 컬럼
  - `id`: `bigIncrements`, not null, default auto increment
  - `conversion_date`: `date`, not null, composite index
  - `conversion_datetime`: `dateTime`, not null
  - `access_page`: `string(255)`, not null, composite index
  - `conversion_type`: `string(50)`, not null, index
  - `target_page`: `string(1000)`, null, default `null`
  - `referer_host`: `string(255)`, not null, index
  - `device_type`: `string(20)`, not null, composite index
  - `device_brand`: `string(50)`, null, default `null`
  - `device_model`: `string(100)`, null, default `null`
  - `os`: `string(50)`, null, default `null`
  - `browser`: `string(50)`, null, default `null`
  - `ip`: `ipAddress`, not null, index
  - `referer_url`: `string(2048)`, null, default `null`
  - `user_agent`: `string(512)`, not null
  - `session_id`: `string(120)`, null, default `null`, index
  - `user_idx`: `unsignedBigInteger`, null, default `null`, index

### 4.23 `jobs` -> `th_jobs`

- 용도
  - Laravel database queue 작업 저장
- PK
  - `id`
- 주요 컬럼
  - `queue`: 큐 이름, index
  - `attempts`: 시도 횟수
  - `available_at`: 실행 가능 시각
- 전체 컬럼
  - `id`: `bigIncrements`, not null, default auto increment
  - `queue`: `string`, not null, index
  - `payload`: `longText`, not null
  - `attempts`: `unsignedTinyInteger`, not null
  - `reserved_at`: `unsignedInteger`, null, default `null`
  - `available_at`: `unsignedInteger`, not null
  - `created_at`: `unsignedInteger`, not null

### 4.24 `failed_jobs` -> `th_failed_jobs`

- 용도
  - 실패한 queue 작업 저장
- PK
  - `id`
- 주요 컬럼
  - `uuid`: 실패 작업 UUID, unique
  - `exception`: 예외 정보
  - `failed_at`: 실패 시각, 기본값 `CURRENT_TIMESTAMP`
- 전체 컬럼
  - `id`: `id`, not null, default auto increment
  - `uuid`: `string`, not null, unique
  - `connection`: `text`, not null
  - `queue`: `text`, not null
  - `payload`: `longText`, not null
  - `exception`: `longText`, not null
  - `failed_at`: `timestamp`, not null, default `CURRENT_TIMESTAMP`

## 5. 작성 메모

- 실제 서비스 기준 해석은 `mysql` 연결과 `th_` prefix 기준
- `Schema::create('users')` 같은 선언명과 실제 MySQL 테이블명은 다를 수 있음
- FK 제약은 마이그레이션에 명시적으로 걸려 있지 않은 경우가 많아, 컬럼명/주석 기준 논리 관계로 분리 정리
- `post_histories`는 초기 `view_datetime`, `view_user_idx`에서 최종적으로 `create_datetime`, `create_user_idx`로 변경된 상태 기준
- `users.level` 기본값은 코드상 `nomal`이므로 문서에도 실제 코드값 그대로 기록
