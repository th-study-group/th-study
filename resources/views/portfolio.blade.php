@extends('layouts.app')

@section('title', '포트폴리오')

@section('style')
@endsection

@push('styles')
    <link href="{{ asset('css/intro/portfolio.css') }}" rel="stylesheet" />
@endpush

@section('content')
<main class="col-12 p-0 portfolio-page">
<header class="hero py-5">
  <div class="container py-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <div class="pill mb-3"><i class="bi bi-stars"></i><span>티에이치스터디</span></div>
        <h1 class="hero-title fw-bold mb-3">나를 뛰어넘는 <span class="accent">개발자</span>, 운영까지 설계하는 개발자.</h1>
        <p class="fs-5 text-white-50 mb-2 hero-kicker">개발자 성장 플랫폼</p>
        <p class="lead text-white-50 mb-4 hero-sub">구현을 넘어 운영·기획·디자인·브랜드까지 확장하며, 서비스를 직접 만들고 운영하는 역량을 쌓는다.</p>
        <div class="d-flex flex-wrap gap-3 no-print">
          <a class="btn btn-primary btn-lg rounded-4 px-4" href="https://www.th-study.com/" target="_blank" rel="noreferrer"><i class="bi bi-globe2 me-2"></i>사이트</a>
          <a class="btn btn-outline-light btn-lg rounded-4 px-4" href="https://github.com/th-study-group/th-study" target="_blank" rel="noreferrer"><i class="bi bi-github me-2"></i>GitHub</a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="hero-card p-4">
          <div class="fw-bold text-white mb-2">요약</div>
          <ul class="text-white-50 mb-3" style="margin-left:18px;">
            <li>운영: AWS Lightsail Ubuntu 직접 구성</li>
            <li>검증: 로컬 환경 및 Docker Compose 이용하여 테스트</li>
            <li>백업: <code style="color:#fff;">/backup/mysql</code> 14일 보관</li>
          </ul>
          <div class="fw-bold text-white mb-2">Tech Stack</div>
          <div class="d-flex flex-wrap gap-3 align-items-center icons">
            <i class="devicon-php-plain"></i><i class="devicon-laravel-original"></i><i class="devicon-mysql-original"></i>
            <i class="devicon-nginx-original"></i><i class="devicon-ubuntu-plain"></i><i class="devicon-docker-plain"></i><i class="devicon-bootstrap-plain"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<section class="section">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <h2 class="h2x mb-3">목차</h2>
        <p class="leadx mb-0">표, 흐름도, 운영 문서, README 원문까지 포함</p>
      </div>
      <div class="col-lg-8">
        <div class="box pad toc">
          <ul>
            <li><a href="#overview">1. 개요</a><span class="toc-dots"></span><span class="toc-desc">방향/슬로건</span></li>
            <li><a href="#versions">2. 버전</a><span class="toc-dots"></span><span class="toc-desc">Laravel/PHP/Node 등</span></li>
            <li><a href="#note-module">3. 블로그 서비스 모듈 구축</a><span class="toc-dots"></span><span class="toc-desc">CRUD 기반 설계/검증/권한</span></li>
            <li><a href="#flows">4. 핵심 흐름</a><span class="toc-dots"></span><span class="toc-desc">메일/배포</span></li>
            <li><a href="#pwa-push">5. PWA 설치/푸시</a><span class="toc-dots"></span><span class="toc-desc">허용/구독/캐시 대응</span></li>
            <li><a href="#run">6. 실행</a><span class="toc-dots"></span><span class="toc-desc">로컬/큐</span></li>
            <li><a href="#deploy">7. 배포</a><span class="toc-dots"></span><span class="toc-desc">SSH + git pull</span></li>
            <li><a href="#infra">8. 운영 인프라</a><span class="toc-dots"></span><span class="toc-desc">Lightsail + Swap</span></li>
            <li><a href="#backup">9. DB 백업</a><span class="toc-dots"></span><span class="toc-desc">14일 정책</span></li>
            <li><a href="#docker">10. 개발 검증 Docker</a><span class="toc-dots"></span><span class="toc-desc">compose on/off</span></li>
            <li><a href="#queue-service">11. Queue 영구 실행 systemd</a><span class="toc-dots"></span><span class="toc-desc">서비스 등록</span></li>
            <li><a href="#codex-skill">12. Codex 스킬</a><span class="toc-dots"></span><span class="toc-desc">SKILL.md 작성법과 파일 종류</span></li>
            <li><a href="#appendix">13. README 원문</a><span class="toc-dots"></span><span class="toc-desc">전체 포함</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="overview" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">1. 개요</h2>
    <div class="box pad">
      <p class="leadx mb-2">티에이치스터디는 기능 구현에서 끝나지 않고, 운영까지 포함해서 “서비스를 굴리는 경험”을 축적하는 프로젝트다.</p>
      <div class="row g-3">
        <div class="col-md-4"><div class="p-3 border rounded-4"><div class="fw-bold">나를 뛰어넘는 개발자</div><div class="text-secondary mt-1">구조/운영까지 확장</div></div></div>
        <div class="col-md-4"><div class="p-3 border rounded-4"><div class="fw-bold">나를 뛰어넘어 성장하는 개발자</div><div class="text-secondary mt-1">기록 → 개선 → 반복</div></div></div>
        <div class="col-md-4"><div class="p-3 border rounded-4"><div class="fw-bold">장인정신을 지닌 개발자</div><div class="text-secondary mt-1">속도 + 구조 + 운영 균형</div></div></div>
      </div>
      <div class="callout mt-3">
        <strong>범위</strong><br>
        기획/디자인/개발/배포/백업/로그까지 한 프로젝트 안에서 전부 경험하고 문서로 남깁니다.
        <br>
        기술 역량은 PHP/Laravel에 한정하지 않고 Java 기반 백엔드와 JavaScript 생태계까지 확장합니다.
      </div>
    </div>
  </div>
</section>

<section id="versions" class="section">
  <div class="container">
    <h2 class="h2x mb-3">2. 버전</h2>
    <div class="box pad">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:28%">항목</th><th>버전</th></tr></thead>
          <tbody>
            <tr><td class='fw-bold'>Laravel</td><td>10</td></tr>
            <tr><td class='fw-bold'>PHP</td><td>8.2</td></tr>
            <tr><td class='fw-bold'>MySQL</td><td>8.0.45</td></tr>
            <tr><td class='fw-bold'>Ubuntu</td><td>Ubuntu 22.04</td></tr>
            <tr><td class='fw-bold'>Nginx</td><td>1.18.0</td></tr>
            <tr><td class='fw-bold'>Node.js</td><td>20.20.0</td></tr>
            <tr><td class='fw-bold'>Vite</td><td>5.4.21</td></tr>
            <tr><td class='fw-bold'>Bootstrap</td><td>5</td></tr>
            <tr><td class='fw-bold'>Docker</td><td>27.5.1</td></tr>
          </tbody>
        </table>
      </div>
      <div class="callout mt-4">
        <strong>프론트 라이브러리 구성</strong><br>
        사용 라이브러리: Bootstrap 5, jQuery 3.7.1, Flatpickr, Bootstrap Icons, Devicon
        <br>
        기준 파일:
        <br><code>resources/views/layouts/app.blade.php</code>
        <br><code>resources/views/partials/head-styles.blade.php</code>
        <br><code>resources/views/partials/head-scripts.blade.php</code>
      </div>
    </div>
  </div>
</section>

<section id="note-module" class="section">
  <div class="container">
    <h2 class="h2x mb-3">3. 블로그 서비스 모듈 구축</h2>
    <div class="box pad">
      <p class="leadx mb-3">블로그 서비스 기능은 단순 입력 화면이 아니라 운영 가능한 모듈로 설계했습니다. 검증, 권한, 파일 업로드, 이력, 태그 매핑을 분리 구조로 구현했습니다.</p>
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">영역</th>
              <th>구현 내용</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold">아키텍처</td>
              <td>Controller - Service - Repository 구조로 분리, FormRequest 기반 검증, Policy 기반 권한 제어(admin 전용 작성/수정/삭제)</td>
            </tr>
            <tr>
              <td class="fw-bold">주제 조회</td>
              <td>그룹-카테고리-토픽 관계를 with 조회로 연결하고 <code>use_flag=1</code> 토픽만 노출</td>
            </tr>
            <tr>
              <td class="fw-bold">썸네일 처리</td>
              <td>Intervention Image 적용, EXIF 회전 보정, 1600px 축소, PNG 유지/JPG 압축, <code>storage/app/public/YYYYMM/YmdHis.ext</code> 저장(동초 충돌 시 접미사 부여)</td>
            </tr>
            <tr>
              <td class="fw-bold">해시태그</td>
              <td>최대 10개, 항목당 20자 검증. <code>note_tags</code>와 <code>note_tag_map</code> 다대다 저장</td>
            </tr>
            <tr>
              <td class="fw-bold">공개/삭제 정책</td>
              <td>일반 사용자는 공개 글만 조회 가능하고 비공개 상세는 404 처리. 삭제는 admin만 가능하며 비공개 상태에서만 허용</td>
            </tr>
            <tr>
              <td class="fw-bold">편집 UX</td>
              <td>등록/수정 단일 뷰 재사용, 수정 시 기존 썸네일/태그 preload, 썸네일 삭제와 태그 삭제는 AJAX로 즉시 반영</td>
            </tr>
            <tr>
              <td class="fw-bold">콘텐츠 저장/출력</td>
              <td>Toast UI 저장 포맷을 Markdown에서 HTML로 전환하고, 상세 화면은 기존 Markdown 데이터도 자동 변환해 호환 렌더링</td>
            </tr>
            <tr>
              <td class="fw-bold">보안 정제</td>
              <td>서버 저장 전 sanitize 적용(허용 태그 중심), 위험 태그/이벤트 속성 제거 및 <code>javascript:</code> 링크 차단</td>
            </tr>
            <tr>
              <td class="fw-bold">히스토리</td>
              <td>블로그 글 등록 시 이벤트 기반으로 <code>note_histories</code> 기록(작업구분, IP, UA, referer 포함)</td>
            </tr>
            <tr>
              <td class="fw-bold">태그/파일 정리</td>
              <td>orphan 태그는 soft delete 후 재사용 시 restore, 글 삭제 시 썸네일 파일과 태그 매핑도 함께 정리</td>
            </tr>
            <tr>
              <td class="fw-bold">운영 이슈 대응</td>
              <td>로컬 업로드 실패 원인 분석 후 php.ini 업로드 한도(2M/8M -> 50M/50M) 조정</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="callout mt-4">
        <strong>최근 고도화 핵심</strong>
        <ul class="mb-0 mt-2">
          <li>목록은 초기 SSR + AJAX 스크롤 페이징(10건 단위) 구조로 정리</li>
          <li>검색은 FormRequest로 검증하고, 목록/상세는 공통 AJAX 흐름으로 통일</li>
          <li>수정 화면에서는 기존 썸네일과 태그를 preload 하고, 개별 삭제도 AJAX로 처리</li>
          <li>라우트 그룹명 `blogs`와 DB 그룹 코드 `blog`는 `config/note.php` 매핑으로 연결</li>
          <li>`/blogs`는 전체 카테고리, `/blogs/{slug}`는 단일 카테고리 조회로 분기하며 잘못된 slug는 404 처리</li>
          <li>상세 공유 대응을 위해 메타/OG를 페이지 상속 구조로 정리</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section id="flows" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">4. 핵심 흐름</h2>
    <div class="row g-3">
      <div class="col-lg-6"><div class="box pad h-100"><div class="fw-bold mb-2"><i class="bi bi-envelope me-2"></i>메일/큐 흐름</div><div class="svg-wrap">
<svg class="svg-flow" viewBox="0 0 980 160" role="img" aria-label="메일/큐 흐름도">
  <defs><marker id="arrow2" markerWidth="12" markerHeight="12" refX="10" refY="6" orient="auto">
    <path d="M0,0 L12,6 L0,12 z" fill="#111827"></path></marker></defs>
  <g font-family="system-ui, -apple-system" font-size="18" fill="#111827">
    <rect x="30"  y="50" width="160" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="55" y="90">Controller</text>
    <rect x="220" y="50" width="160" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="272" y="90">Service</text>
    <rect x="410" y="50" width="160" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="462" y="82">Job</text><text x="440" y="104">(Queue)</text>
    <rect x="600" y="50" width="160" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="635" y="82">Listener</text><text x="632" y="104">(Optional)</text>
    <rect x="790" y="50" width="160" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="820" y="82">Mail +</text><text x="815" y="104">mail_logs</text>
    <line x1="190" y1="82" x2="220" y2="82" stroke="#111827" stroke-width="2.2" marker-end="url(#arrow2)"/>
    <line x1="380" y1="82" x2="410" y2="82" stroke="#111827" stroke-width="2.2" marker-end="url(#arrow2)"/>
    <line x1="570" y1="82" x2="600" y2="82" stroke="#111827" stroke-width="2.2" marker-end="url(#arrow2)"/>
    <line x1="760" y1="82" x2="790" y2="82" stroke="#111827" stroke-width="2.2" marker-end="url(#arrow2)"/>
  </g>
</svg>
</div></div></div>
      <div class="col-lg-6"><div class="box pad h-100"><div class="fw-bold mb-2"><i class="bi bi-rocket-takeoff me-2"></i>배포 흐름</div><div class="svg-wrap">
<svg class="svg-flow" viewBox="0 0 1100 180" role="img" aria-label="배포 흐름도">
  <defs><marker id="arrow" markerWidth="12" markerHeight="12" refX="10" refY="6" orient="auto">
    <path d="M0,0 L12,6 L0,12 z" fill="#111827"></path></marker></defs>
  <g font-family="system-ui, -apple-system" font-size="18" fill="#111827">
    <circle cx="85" cy="90" r="40" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="62" y="96">Local</text>
    <rect x="165" y="58" width="180" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="205" y="96">GitHub push</text>
    <rect x="375" y="58" width="180" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="440" y="96">SSH</text>
    <rect x="585" y="58" width="180" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/><text x="638" y="96">git pull</text>
    <rect x="795" y="58" width="270" height="64" rx="14" fill="#fff" stroke="#cbd5e1" stroke-width="2"/>
    <text x="825" y="88">build / migrate /</text><text x="865" y="110">restart</text>
    <line x1="125" y1="90" x2="165" y2="90" stroke="#111827" stroke-width="2.4" marker-end="url(#arrow)"/>
    <line x1="345" y1="90" x2="375" y2="90" stroke="#111827" stroke-width="2.4" marker-end="url(#arrow)"/>
    <line x1="555" y1="90" x2="585" y2="90" stroke="#111827" stroke-width="2.4" marker-end="url(#arrow)"/>
    <line x1="765" y1="90" x2="795" y2="90" stroke="#111827" stroke-width="2.4" marker-end="url(#arrow)"/>
  </g>
</svg>
</div></div></div>
    </div>
  </div>
</section>

<section id="pwa-push" class="section">
  <div class="container">
    <h2 class="h2x mb-3">5. PWA 설치/푸시</h2>
    <div class="box pad">
      <p class="leadx mb-3">PWA 설치부터 구독, 발송, 클릭 추적까지 한 흐름으로 운영합니다.</p>
      <ul class="small text-muted mb-3">
        <li>로그인 시 <code>exists</code> 확인 후 없으면 재등록, 권한 허용 상태에서만 신규 구독 생성</li>
        <li><code>ping</code>은 로그인 직후 1회만 수행해 최근접속시각을 갱신</li>
        <li>발송 이력에 성공/실패와 실패 사유 JSON을 함께 기록</li>
      </ul>
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:20%">단계</th>
              <th>설명</th>
              <th style="width:34%">기준 코드</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold">1. 설치</td>
              <td>서비스워커 등록 후 PWA 앱 설치 컨텍스트에서 실행</td>
              <td><code>resources/views/layouts/app.blade.php</code>, <code>public/service-worker.js</code></td>
            </tr>
            <tr>
              <td class="fw-bold">2. 구독 동기화</td>
              <td>로그인 시 <code>exists</code>로 서버 상태를 확인하고, 없으면 재등록. 구독 미존재+권한 허용 시 생성하며 <code>ping</code>은 로그인 직후 1회만 갱신</td>
              <td><code>public/js/pwa_push.js</code> (<code>autoSyncOnLogin</code>)</td>
            </tr>
            <tr>
              <td class="fw-bold">2-1. 허용 팝업</td>
              <td>로그인 + 홈화면 추가(standalone) 앱에서만 허용 팝업 노출, 허용 클릭 시 권한 요청 실행</td>
              <td><code>public/js/pwa_push.js</code> (<code>openNativePushPermissionPrompt</code>), <code>resources/views/layouts/header.blade.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">2-2. OS 설정 위치</td>
              <td>iOS: 설정 &gt; 알림 &gt; 티에이치스터디 / Android: 설정 &gt; 앱(또는 Chrome 사이트 설정) &gt; 알림에서 수동 허용 가능</td>
              <td><code>README.md</code> (운영 가이드), 사용자 디바이스 OS 알림 설정</td>
            </tr>
            <tr>
              <td class="fw-bold">3. 발송</td>
              <td>서비스에서 사용자별 Job 등록, Job에서 WebPush 전송</td>
              <td><code>app/Services/PushService.php</code>, <code>app/Jobs/SendWebPushJob.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">4. 이력 기록</td>
              <td>발송/클릭 토큰/대상 URL/테이블명 + 성공여부(<code>success_flag</code>) + 실패사유 JSON(<code>send_error_message</code>) 기록</td>
              <td><code>web_push_messages</code>, <code>app/Models/WebPushMessage.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">5. 클릭 이동</td>
              <td><code>/push/open/{token}</code>으로 클릭률 기록 후 <code>target_url</code>로 이동</td>
              <td><code>app/Http/Controllers/PushController.php</code>, <code>app/Services/PushService.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">5-1. PWA 링크/이미지 예외 처리</td>
              <td>Standalone PWA에서는 외부 URL을 안내 모달로 한 번 감싸고, 이미지 파일 URL/본문 이미지는 앱 내부 미리보기 모달로 열어 닫기 UI 부재 문제를 완화</td>
              <td><code>public/js/pwa.js</code>, <code>resources/views/partials/pwa-popup.blade.php</code>, <code>public/css/pwa-modal.css</code></td>
            </tr>
            <tr>
              <td class="fw-bold">6. iPhone 캐시 대응</td>
              <td>정적 JS/CSS와 서비스워커 URL에 <code>filemtime</code> 기반 버전 쿼리를 붙여 운영 캐시 고착을 방지</td>
              <td><code>resources/views/partials/head-scripts.blade.php</code>, <code>resources/views/partials/head-styles.blade.php</code>, <code>resources/views/layouts/app.blade.php</code></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section id="run" class="section">
  <div class="container">
    <h2 class="h2x mb-3">6. 실행</h2>
    <div class="box pad">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 로컬 실행(예시)</span><button class="copybtn no-print" onclick="copyFrom('#localRun', this)">복사</button></div>
      <pre id="localRun"><code># 의존성 설치
composer install
npm install

# 환경파일 준비
cp .env.example .env
php artisan key:generate

# DB 반영 + 시드
php artisan migrate --seed

# 개발 서버
npm run dev
php artisan serve --host=0.0.0.0 --port=8000</code></pre>
    </div>
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 큐 상태/점검(운영 예시)</span><button class="copybtn no-print" onclick="copyFrom('#queueRun', this)">복사</button></div>
      <pre id="queueRun"><code># 서비스 상태 확인
sudo systemctl status th-study-queue

# 로그 확인
journalctl -u th-study-queue -f

# 실패 작업 확인(필요 시)
php artisan queue:failed</code></pre>
    </div>
    </div></div>
  </div>
</section>

<section id="deploy" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">7. 배포</h2>
    <div class="box pad">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 서버 접속</span><button class="copybtn no-print" onclick="copyFrom('#sshCmd', this)">복사</button></div>
      <pre id="sshCmd"><code>ssh [유저명or아이디]@[서버IP]
# 예) ssh ubuntu@1.2.3.4</code></pre>
    </div>
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 배포(서버에서 실행)</span><button class="copybtn no-print" onclick="copyFrom('#deployCmd', this)">복사</button></div>
      <pre id="deployCmd"><code>cd /var/www/th-study

# 최신 코드
git pull origin main

# PHP 의존성(운영용)
composer install --no-dev -o

# 프론트 빌드
npm ci
npm run build

# DB 반영
php artisan migrate --force

# 슈퍼어드민 보정(환경별 택1)
php artisan db:seed --class=EnvSuperAdminSeeder --force
# php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화
php artisan db:seed --class=NoteMasterSeeder --force

# 큐 서비스 재시작(배포 반영)
sudo systemctl restart th-study-queue

# 웹서버 리로드(필요 시)
sudo systemctl reload nginx</code></pre>
    </div>
    </div>
    <div class="callout mt-4">
      <strong>CI/CD 운영 기준</strong><br>
      <ul class="mb-0" style="margin-left:18px;">
        <li>Self-hosted runner 기준으로 <code>main</code> push 시 배포 자동화 구성</li>
        <li>코드 동기화 -> <code>composer install --no-dev</code> -> <code>php artisan optimize:clear</code>/<code>config:cache</code>/<code>route:cache</code>/<code>view:cache</code> -> <code>php artisan migrate --force</code> -> <code>php artisan db:seed --class=NoteMasterSeeder --force</code></li>
        <li>슈퍼어드민 계정은 환경별 시더로 보정: 운영 <code>AutoSuperAdminSeeder</code>, 개발/로컬 <code>EnvSuperAdminSeeder</code></li>
        <li>노트 마스터 데이터는 <code>config/seeders/note.php</code>에서 관리하고, 시더는 <code>updateOrCreate</code> 방식으로 동기화</li>
        <li>웹서버 reload(<code>php8.2-fpm</code>, <code>nginx</code>) 및 큐 반영(<code>php artisan queue:restart</code>) 포함</li>
      </ul>
      <div class="codeblock mt-3">
        <div class="codehdr"><span>bash · 수동 실행(슈퍼어드민/노트)</span><button class="copybtn no-print" onclick="copyFrom('#seedOps', this)">복사</button></div>
        <pre id="seedOps"><code># 개발/로컬: .env 기준 슈퍼어드민 보정
php artisan db:seed --class=EnvSuperAdminSeeder --force

# 운영: 랜덤 비밀번호 출력 방식 슈퍼어드민 보정
php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
php artisan db:seed --class=NoteMasterSeeder --force</code></pre>
      </div>
    </div>
    </div></div>
  </div>
</section>

<section id="infra" class="section">
  <div class="container">
    <h2 class="h2x mb-3">8. 운영 인프라 AWS Lightsail</h2>
    <div class="box pad">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:22%">항목</th><th>값</th></tr></thead>
          <tbody>
            <tr><td class="fw-bold">서버</td><td>AWS Lightsail / Ubuntu</td></tr>
            <tr><td class="fw-bold">웹</td><td>Nginx + PHP-FPM</td></tr>
            <tr><td class="fw-bold">DB</td><td>MySQL</td></tr>
            <tr><td class="fw-bold">운영 특징</td><td>도커 없이 직접 설치 운영</td></tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · Swap 2GB(메모리 보강)</span><button class="copybtn no-print" onclick="copyFrom('#swapCmd', this)">복사</button></div>
      <pre id="swapCmd"><code>sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile

swapon --show
free -h</code></pre>
    </div>
    </div>
    <div class="mt-4">
      <div class="codeblock">
        <div class="codehdr"><span>bash · SSL 설정(Certbot + Nginx)</span><button class="copybtn no-print" onclick="copyFrom('#sslCmd', this)">복사</button></div>
        <pre id="sslCmd"><code>sudo apt update
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d example.com -d www.example.com
sudo certbot renew --dry-run</code></pre>
      </div>
    </div>
    <div class="mt-4">
      <div class="codeblock">
        <div class="codehdr"><span>bash · 빠른 운영 점검</span><button class="copybtn no-print" onclick="copyFrom('#opsQuick', this)">복사</button></div>
          <pre id="opsQuick"><code>php artisan about --only=environment,drivers
php artisan migrate:status
sudo systemctl status php8.2-fpm
sudo systemctl status nginx
sudo systemctl status th-study-queue</code></pre>
        </div>
      </div>
    <div class="callout mt-4">
      <strong>운영 경로 기준</strong><br>
      <ul class="mb-0" style="margin-left:18px;">
        <li>앱 루트: <code>/var/www/th-study</code></li>
        <li>Nginx 로그: <code>/var/log/nginx/access.log</code>, <code>/var/log/nginx/error.log</code></li>
        <li>Laravel 로그: <code>/var/www/th-study/storage/logs</code></li>
      </ul>
    </div>
    <div class="callout mt-3">
      <strong>GitHub 조직 운영 기준</strong><br>
      <ul class="mb-0" style="margin-left:18px;">
        <li>Organization/Team 기반 권한 Read/Write/Admin 분리</li>
        <li><code>main</code> 브랜치 보호 규칙 적용</li>
        <li>배포 토큰 최소 권한 원칙 적용</li>
      </ul>
    </div>
    </div>
  </div>
</section>

<section id="backup" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">9. DB 백업</h2>
    <div class="box pad">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:18%">구분</th><th>경로</th><th style="width:22%">주기</th><th style="width:22%">보관</th></tr></thead>
          <tbody>
            <tr><td class="fw-bold">Full</td><td><code>/backup/mysql/full</code></td><td>1일 1회</td><td>14일</td></tr>
            <tr><td class="fw-bold">Binlog</td><td><code>/backup/mysql/binlog</code></td><td>매시간</td><td>14일</td></tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 14일 지난 백업 삭제</span><button class="copybtn no-print" onclick="copyFrom('#bkClean', this)">복사</button></div>
      <pre id="bkClean"><code>find /backup/mysql -type f -mtime +14 -delete</code></pre>
    </div>
    </div>
    </div>
  </div>
</section>

<section id="docker" class="section">
  <div class="container">
    <h2 class="h2x mb-3">10. 개발 검증 Docker</h2>
    <div class="box pad">
      <p class="leadx mb-2">배포 전 동일한 Ubuntu 기반 환경을 검증하기 위해 Docker Compose를 사용합니다.</p>
      
    <div class="codeblock">
      <div class="codehdr"><span>bash · Docker Compose on/off</span><button class="copybtn no-print" onclick="copyFrom('#dcOnOff', this)">복사</button></div>
      <pre id="dcOnOff"><code># 켜기
docker compose up -d

# 상태
docker compose ps

# 로그
docker compose logs -f

# 내리기
docker compose down</code></pre>
    </div>
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 도커 시더 실행(슈퍼어드민/노트)</span><button class="copybtn no-print" onclick="copyFrom('#dcSeed', this)">복사</button></div>
      <pre id="dcSeed"><code># 개발/로컬: .env 기준 슈퍼어드민 보정
docker exec -it th-app php artisan db:seed --class=EnvSuperAdminSeeder --force

# 운영: 랜덤 비밀번호 출력 방식 슈퍼어드민 보정
docker exec -it th-app php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
docker exec -it th-app php artisan db:seed --class=NoteMasterSeeder --force</code></pre>
    </div>
    </div>
    
      <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 도커 MySQL 접속(예시)</span><button class="copybtn no-print" onclick="copyFrom('#dcMysql', this)">복사</button></div>
      <pre id="dcMysql"><code>docker exec -it th-mysql mysql -u[유저명or아이디] -p

USE [DB이름];
SHOW TABLES;</code></pre>
    </div>
    </div>
    </div>
  </div>
</section>

<section id="queue-service" class="section">
  <div class="container">
    <h2 class="h2x mb-3">11. Queue 영구 실행 systemd</h2>
    <div class="box pad">
      <div class="codeblock">
        <div class="codehdr"><span>bash · 서비스 파일 생성</span><button class="copybtn no-print" onclick="copyFrom('#queueSvcCreate', this)">복사</button></div>
        <pre id="queueSvcCreate"><code>sudo nano /etc/systemd/system/th-study-queue.service</code></pre>
      </div>
      <div class="mt-4">
        <div class="codeblock">
          <div class="codehdr"><span>ini · /etc/systemd/system/th-study-queue.service</span><button class="copybtn no-print" onclick="copyFrom('#queueSvcFile', this)">복사</button></div>
          <pre id="queueSvcFile"><code>[Unit]
Description=Laravel Queue Worker
After=network.target mysql.service php8.2-fpm.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/th-study
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target</code></pre>
        </div>
      </div>
      <div class="mt-4">
        <div class="codeblock">
          <div class="codehdr"><span>bash · 서비스 등록/시작</span><button class="copybtn no-print" onclick="copyFrom('#queueSvcStart', this)">복사</button></div>
          <pre id="queueSvcStart"><code>sudo systemctl daemon-reload
sudo systemctl enable th-study-queue
sudo systemctl start th-study-queue
sudo systemctl status th-study-queue</code></pre>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="codex-skill" class="section">
  <div class="container">
    <h2 class="h2x mb-3">12. Codex 스킬 문서 가이드</h2>
    <div class="box pad">
      <p class="leadx mb-3">
        티에이치스터디는 Codex 스킬을 작업 지침서처럼 사용합니다.
        아래에는 `SKILL.md` 작성 방법, 파일 종류, 현재 저장소에서 운영 중인 스킬 문서를 함께 정리했습니다.
      </p>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">파일 종류</th>
              <th>역할</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold"><code>SKILL.md</code></td>
              <td>필수 파일. 스킬 이름, 설명, 사용 조건, 작업 절차를 담는 본문</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>agents/openai.yaml</code></td>
              <td>권장 파일. <code>display_name</code>, <code>short_description</code>, <code>default_prompt</code> 같은 UI 메타데이터 관리</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>scripts/*</code></td>
              <td>선택 파일. 반복되는 처리 로직이나 변환 작업을 실행 스크립트로 고정</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>references/*</code></td>
              <td>선택 파일. 긴 정책 문서, API 문서, 스키마를 필요할 때만 읽도록 분리</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>assets/*</code></td>
              <td>선택 파일. 템플릿, 샘플 결과물, 이미지 같은 출력 리소스 저장</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="callout mt-4">
        <strong><code>SKILL.md</code> 작성 방법</strong>
        <ul class="mb-0 mt-2">
          <li>목적을 한 줄로 먼저 정리합니다.</li>
          <li>frontmatter에 <code>name</code>, <code>description</code>를 작성합니다.</li>
          <li>본문에는 사용 시점, 작업 절차, 참고 자료, 스크립트 사용 위치를 적습니다.</li>
          <li>긴 설명은 <code>references/</code>로, 반복 작업은 <code>scripts/</code>로 분리합니다.</li>
          <li>스킬 폴더 안에는 별도 README를 만들지 않는 것을 기준으로 합니다.</li>
        </ul>
      </div>

      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">현재 스킬 파일</th>
              <th>종류 / 역할</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold"><code>skill/게시판.md</code></td>
              <td>도메인 규격형 스킬. 게시판 CRUD, 권한, 히스토리, 로그, 페이징, 메일 규칙 정의</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>skill/노트.md</code></td>
              <td>기능 명세형 스킬. 노트 메뉴 구조, 라우팅, 권한, 썸네일, 해시태그, 히스토리 규칙 정의</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>skill/백엔드 기초.md</code></td>
              <td>공통 개발 기준 스킬. PHP/Laravel 환경과 컨트롤러-서비스-레포지토리 계층 규칙 안내</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>skill/프론트엔드 기초.md</code></td>
              <td>퍼블리싱 기준 스킬. Bootstrap 5 중심의 프론트 작업 원칙 안내</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section id="appendix" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">13. README 원문</h2>
    <div class="box pad readme-summary">
      <p class="fw-bold mb-2">포트폴리오 관점 요약</p>
      <ul>
        <li>Laravel 10 기반 개발자 성장 플랫폼을 기획부터 운영까지 직접 구축</li>
        <li>핵심 아키텍처는 Controller-Service-Repository 분리와 정책 기반 접근 제어</li>
        <li>메일/큐/로그/백업 포함 운영 흐름을 실서비스 수준으로 문서화</li>
        <li>배포는 SSH + git pull + migrate + systemd queue 재시작 기준으로 표준화</li>
      </ul>
    </div>
    <details class="readme-toggle mt-3">
      <summary>README 원문 펼쳐보기</summary>
      <div class="box pad readme mt-3">
      <h1 id="readme-티에이치-스터디---개발자성장플랫폼">티에이치 스터디 - 개발자성장플랫폼</h1>
<p>Laravel 기반 개인 개발 플랫폼입니다. 기록, 운영, 커뮤니케이션 기능을 하나로 묶어 성장 과정을 서비스 형태로 관리합니다.</p>
<p>이 문서는 기능이 커져도 빠르게 구조를 파악할 수 있도록 <strong>핵심 정책과 동작 원리만</strong> 정리합니다.</p>
<h2 id="readme-1-핵심-기능">1. 핵심 기능</h2>
<ul>
<li>회원가입, 로그인/로그아웃, 이메일 인증</li>
<li>비밀번호 찾기(메일 링크), 비밀번호 변경(요청/완료)</li>
<li>사용자 계정 관리(내 정보 수정, 탈퇴)</li>
<li>공지 게시판(사용자 조회, 관리자 CRUD/공개여부 관리)</li>
<li>문의 게시판(사용자 등록/조회, 관리자 상태 관리)</li>
<li>비로그인 게스트 문의 접수 및 관리자 처리</li>
<li>댓글(일반 게시글 및 운영 답변 흐름)</li>
<li>메일 발송/수신 로그, 로그인 로그, 게시글 히스토리 로그</li>
</ul>
<h3 id="readme-브랜드-슬로건">브랜드 슬로건</h3>
<ul>
<li>나를 뛰어넘는 개발자</li>
<li>나를 뛰어넘는 성장하는 개발자</li>
<li>개발을 넘어 브랜드 기획, 디자인, 광고, 애드센스까지 확장하는 개발자</li>
</ul>
<h3 id="readme-homebladephp-introbladephp-역할"><code>home.blade.php</code> / <code>intro.blade.php</code> 역할</h3>
<ul>
<li><code>resources/views/home.blade.php</code>: 대외 첫인상 랜딩 페이지</li>
<li><code>resources/views/intro.blade.php</code>: 브랜드 철학과 경험을 서사형으로 전달하는 소개 페이지</li>
<li>운영 기준: <code>home</code>은 빠른 이해, <code>intro</code>는 깊은 공감과 신뢰 형성에 초점</li>
</ul>
<h2 id="readme-2-기술-스택">2. 기술 스택</h2>
<ul>
<li>Backend: PHP 8.2, Laravel 10</li>
<li>DB: MySQL</li>
<li>Frontend: Blade, Bootstrap 5, jQuery</li>
<li>Date UI: Flatpickr</li>
<li>Queue: Database queue</li>
<li>Realtime(실험): Pusher + Laravel Echo</li>
<li>Infra: Ubuntu self-hosted deploy</li>
</ul>
<h3 id="readme-프론트-라이브러리-구성appbladephp-기준">프론트 라이브러리 구성 <code>app.blade.php</code> 기준</h3>
<p>로딩 기준 파일:</p>
<ul>
<li><code>resources/views/layouts/app.blade.php</code></li>
<li><code>resources/views/partials/head-styles.blade.php</code></li>
<li><code>resources/views/partials/head-scripts.blade.php</code></li>
</ul>
<p>사용 중인 라이브러리:</p>
<ul>
<li>UI Framework: Bootstrap 5 (<code>public/css/bootstrap.min.css</code>, <code>public/js/bootstrap.bundle.min.js</code>)</li>
<li>DOM/이벤트: jQuery 3.7.1 (<code>public/js/jquery-3.7.1.min.js</code>)</li>
<li>Date Picker: Flatpickr (CDN)</li>
<li>Icon: Bootstrap Icons (CDN)</li>
<li>Tech Icon: Devicon (CDN)</li>
</ul>
<p>외부 에셋/아이콘 출처:</p>
<ul>
<li>Unsplash: <code>https://unsplash.com/ko</code></li>
<li>Devicon: <code>https://devicon.dev/</code></li>
</ul>
<h2 id="readme-3-디렉터리-빠른-가이드">3. 디렉터리 빠른 가이드</h2>
<div class="codeblock"><div class="codehdr"><span>text</span></div><pre><code>app/
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
  emails/            # 메일 템플릿</code></pre></div>
<h2 id="readme-4-요청-처리-원리">4. 요청 처리 원리</h2>
<p>기본 흐름은 아래와 같습니다.</p>
<ol>
<li><code>Route</code>가 URL을 컨트롤러로 연결</li>
<li><code>FormRequest</code>가 입력 검증</li>
<li><code>Controller</code>가 <code>Service</code> 호출</li>
<li><code>Service</code>가 도메인 규칙 처리</li>
<li><code>Repository</code>가 DB 조회/저장 수행</li>
<li>결과를 Blade 또는 JSON으로 응답</li>
</ol>
<p>핵심 원칙:</p>
<ul>
<li>Controller는 얇게 유지</li>
<li>도메인 규칙은 Service에 집중</li>
<li>DB 쿼리는 Repository에 분리</li>
</ul>
<h2 id="readme-5-라우팅접근-정책">5. 라우팅/접근 정책</h2>
<ul>
<li>공통 웹: <code>routes/web.php</code></li>
<li>인증: <code>routes/auth.php</code></li>
<li>로그인 사용자: <code>routes/login.php</code></li>
<li>관리자: <code>routes/admin.php</code></li>
<li>공개 게시판/게스트문의: <code>routes/user.php</code></li>
<li>로컬 개발 테스트: <code>routes/dev.php</code> (<code>local.only</code> 미들웨어)</li>
</ul>
<p>관리자 라우트는 <code>auth + email.verified + level:admin</code> 정책으로 보호됩니다.</p>
<h2 id="readme-6-게시판-타입-정책">6. 게시판 타입 정책</h2>
<p>게시판 타입 정책은 <code>config/board.php</code>로 중앙 관리합니다.</p>
<ul>
<li><code>post_type</code>: 전체 게시판 타입 정의</li>
<li><code>post_type_for_route</code>: 사용자 URL 노출 대상 타입</li>
<li><code>post_type_excluded</code>: 권한/정책 제외 타입</li>
<li><code>post_use_flag</code>: 공개(1)/비공개(0) 라벨</li>
<li><code>status</code>: 문의 처리 상태(<code>wait</code>, <code>in_progress</code>, <code>on_hold</code>, <code>completed</code>)</li>
</ul>
<p>운영 관점:</p>
<ul>
<li>타입을 추가/변경할 때 <code>config/board.php</code>를 기준으로 라우트/뷰/권한을 함께 맞춥니다.</li>
<li>관리자에서 게시글 공개 여부(<code>use_flag</code>)를 제어합니다.</li>
</ul>
<h2 id="readme-7-미들웨어권한-정책">7. 미들웨어/권한 정책</h2>
<h3 id="readme-미들웨어">미들웨어</h3>
<ul>
<li><code>auth</code>: 로그인 사용자만 접근</li>
<li><code>email.verified</code>: 이메일 인증 완료 사용자만 접근</li>
<li><code>level:admin</code>: 관리자 권한 체크</li>
<li><code>note.slug</code>: 콘텐츠 slug 유효성 체크</li>
<li><code>local.only</code>: 로컬 환경/허용 IP에서만 접근</li>
<li><code>ForcePasswordChange</code>: 비밀번호 변경 강제 대상 유저 제어</li>
<li><code>CheckSessionVersion</code>: 개인정보 변경 후 세션 불일치 시 재로그인 강제</li>
</ul>
<h3 id="readme-policy">Policy</h3>
<ul>
<li><code>UserPolicy</code>: 사용자 탈퇴 권한</li>
<li><code>PostPolicy</code>: 게시글 수정/삭제/공개여부 변경 권한</li>
<li><code>CommentPolicy</code>: 댓글 생성/수정/삭제 권한</li>
</ul>
<p>핵심 원칙:</p>
<ul>
<li>미들웨어는 &quot;입구 보안&quot;</li>
<li>Policy는 &quot;행동 권한&quot;</li>
</ul>
<h3 id="readme-슈퍼어드민-시더-정책">슈퍼어드민 시더 정책</h3>
<ul>
<li>기준 파일: <code>database/seeders/DatabaseSeeder.php</code></li>
<li>운영(<code>production</code>)에서는 <code>AutoSuperAdminSeeder</code>를 실행해 슈퍼어드민 계정을 보정</li>
<li>개발/로컬에서는 <code>EnvSuperAdminSeeder</code>를 실행해 <code>.env</code> 기준으로 슈퍼어드민 계정을 보정</li>
<li>두 시더 모두 이메일 기준 <code>firstOrNew</code> 패턴으로 동작해 중복 생성 없이 갱신</li>
<li>비밀번호/토큰/실계정 값은 문서나 저장소에 기록하지 않고 환경변수로만 관리</li>
</ul>
<h2 id="readme-8-메일-시스템">8. 메일 시스템</h2>
<h3 id="readme-81-공통-발송-파이프라인">8.1 공통 발송 파이프라인</h3>
<ol>
<li>컨트롤러/서비스에서 <code>SendMailJob</code> 실행</li>
<li>큐 워커가 메일 발송 수행</li>
<li>발송 직후 <code>MailSentEvent</code> 발생</li>
<li><code>WriteMailLogEventListener</code>가 <code>mail_logs</code> 기록</li>
<li>일부 기능은 메일 링크 진입 시 수신 시각/IP를 업데이트</li>
</ol>
<h3 id="readme-82-메일-템플릿-구성">8.2 메일 템플릿 구성</h3>
<p>템플릿 경로: <code>resources/views/emails</code></p>
<ul>
<li><code>verify_code.blade.php</code>: 회원가입 이메일 인증</li>
<li><code>reset_password.blade.php</code>: 비밀번호 재설정 링크</li>
<li><code>password_change_request.blade.php</code>: 비밀번호 변경 요청 안내</li>
<li><code>password_change_complete.blade.php</code>: 비밀번호 변경 완료 안내</li>
<li><code>withdrawal_notice.blade.php</code>: 회원탈퇴 완료 안내</li>
<li><code>inquiry_created.blade.php</code>: 문의 등록 알림(관리자)</li>
<li><code>inquiry_answered.blade.php</code>: 문의 답변 알림(작성자)</li>
</ul>
<h3 id="readme-83-메일-발송-시나리오">8.3 메일 발송 시나리오</h3>
<ul>
<li>회원가입 후 이메일 인증 메일 발송</li>
<li>계정찾기(비밀번호 찾기) 요청 시 재설정 메일 발송</li>
<li>비밀번호 변경 요청/완료 시 안내 메일 발송</li>
<li>회원탈퇴 완료 시 안내 메일 발송</li>
<li>문의 등록 시 관리자 알림 메일 발송</li>
<li>댓글(답변) 등록 시 작성자 알림 메일 발송</li>
</ul>
<h3 id="readme-84-메일-로그-정책">8.4 메일 로그 정책</h3>
<p><code>mail_logs</code>에 아래 정보를 기록합니다.</p>
<ul>
<li>메일 종류(<code>kind</code>)</li>
<li>수신 이메일(<code>email</code>)</li>
<li>토큰(<code>token</code>, 필요 시)</li>
<li>발송 시각(<code>send_datetime</code>)</li>
<li>수신 확인 시각/아이피(<code>receive_datetime</code>, <code>receive_ip</code>)</li>
</ul>
<p>운영자는 발송 이력과 링크 도달 이력을 분리해서 추적할 수 있습니다.</p>
<h3 id="readme-85-pwa-설치부터-푸시-동작-흐름">8.5 PWA 설치부터 푸시 동작 흐름</h3>
<p>웹 푸시는 설치/구독/발송/클릭 추적까지 한 흐름으로 동작합니다.</p>
<ol>
<li>PWA 설치</li>
</ol>
<ul>
<li>브라우저에서 사이트를 설치(PWA)하면 서비스워커가 활성화됩니다.</li>
<li>설치 앱과 일반 브라우저 탭은 저장소/세션 컨텍스트가 다를 수 있습니다.</li>
</ul>
<ol start="2">
<li>로그인 시 자동 구독 동기화</li>
</ol>
<ul>
<li><code>public/js/pwa_push.js</code>의 <code>autoSyncOnLogin()</code>이 실행됩니다.</li>
<li>구독이 있으면 <code>/push/exists</code>로 서버 존재 여부를 확인하고, 없으면 재등록합니다.</li>
<li>구독이 없고 권한이 허용된 경우 <code>subscribeAndSave()</code>로 새 구독을 만들고 서버(<code>/push/subscribe</code>)에 저장합니다.</li>
<li><code>/push/ping</code>은 로그인 직후 첫 페이지에서만 최근접속시각 갱신 용도로 1회 호출합니다.</li>
</ul>
<p>2-1. 앱 푸시 허용 팝업(Standalone 앱 전용)</p>
<ul>
<li><code>public/js/pwa_push.js</code>의 <code>openNativePushPermissionPrompt()</code>가 실행됩니다.</li>
<li>로그인 상태 + 홈화면 추가로 실행된 PWA 컨텍스트(standalone)에서만 팝업을 노출합니다.</li>
<li>허용 버튼 클릭 시 <code>Notification.requestPermission()</code> 호출 후 구독 생성/저장을 진행합니다.</li>
</ul>
<p>2-2. OS별 푸시 알림 설정 위치(사용자 안내용)</p>
<ul>
<li>iOS: <code>설정 &gt; 알림 &gt; 티에이치스터디(PWA 앱명)</code>에서 알림 허용/배너/사운드 설정</li>
<li>Android(Chrome PWA): <code>설정 &gt; 앱 &gt; TH Study(또는 브라우저 앱) &gt; 알림</code>에서 허용/차단</li>
<li>Android(브라우저 권한): <code>Chrome &gt; 사이트 설정 &gt; 알림 &gt; th-study.com</code>에서 허용/차단</li>
<li>최초 PWA 실행 시 허용을 놓친 경우 위 경로에서 수동으로 다시 켤 수 있습니다.</li>
</ul>
<ol start="3">
<li>구독 유지/정리 정책</li>
</ol>
<ul>
<li>활성 구독은 <code>web_push_subscriptions</code>에 저장됩니다.</li>
<li>계정은 디바이스별 다중 구독을 유지합니다(웹/앱/브라우저별 endpoint 공존).</li>
<li>로그아웃 시에는 현재 디바이스 endpoint 해제를 우선 시도하고, 다른 디바이스 구독은 유지합니다.</li>
<li>회원탈퇴 시에는 해당 사용자의 구독을 서버에서 전체 삭제합니다.</li>
</ul>
<ol start="4">
<li>푸시 발송</li>
</ol>
<ul>
<li>서비스 레이어에서 <code>PushService::sendToUser()</code>를 호출합니다.</li>
<li>대상 사용자별 <code>SendWebPushJob</code>이 큐에 등록되고, Job에서 실제 웹푸시를 전송합니다.</li>
<li>발송 이력은 <code>web_push_messages</code>에 기록되며, <code>success_flag(1/0)</code>와 <code>send_error_message(JSON)</code>에 전송 결과를 남깁니다.</li>
</ul>
<ol start="5">
<li>클릭 추적 및 이동</li>
</ol>
<ul>
<li>푸시 payload URL은 <code>/push/open/{click_token}</code> 형태로 전달됩니다.</li>
<li>클릭 시 <code>click_datetime</code>이 기록되고, <code>target_url</code>로 리다이렉트됩니다.</li>
<li>로그인 만료 시에는 로그인 후 intended 경로로 복귀합니다.</li>
</ul>
<p>5-1. Standalone PWA 링크/이미지 예외 처리</p>
<ul>
<li>일반 웹은 기존 브라우저 동작(<code>target="_blank"</code>, 새 탭/새 창)을 유지합니다.</li>
<li>홈화면 설치 PWA(<code>standalone</code>)에서는 외부 URL 클릭 시 안내 모달을 먼저 노출합니다.</li>
<li>외부 URL 모달은 <code>복사</code>/<code>열기</code>만 제공해 iOS PWA에서 닫기 UI가 없는 문제를 완화합니다.</li>
<li>이미지 파일 URL 및 본문 이미지 클릭은 앱 내부 이미지 미리보기 모달로 처리합니다.</li>
<li>PWA 전용 분기 코드는 <code>public/js/pwa.js</code>, <code>resources/views/partials/pwa-popup.blade.php</code>, <code>public/css/pwa-modal.css</code>로 분리했습니다.</li>
</ul>
<ol start="6">
<li>iPhone 캐시 대응(운영 반영 안정화)</li>
</ol>
<ul>
<li>iOS Safari/PWA는 JS/Service Worker 캐시가 강하게 남을 수 있어, 정적 에셋 버전 파라미터를 적용합니다.</li>
<li><code>resources/views/partials/head-scripts.blade.php</code>와 <code>resources/views/partials/head-styles.blade.php</code>에서 <code>filemtime(...)</code> 기반 <code>?v=</code>를 사용합니다.</li>
<li><code>resources/views/layouts/app.blade.php</code>의 서비스워커 등록 URL에도 <code>?v=</code>를 붙이고 <code>reg.update()</code>를 호출합니다.</li>
<li>배포 시 <code>php artisan optimize:clear</code>를 실행해 서버 캐시를 정리합니다.</li>
</ul>
<h2 id="readme-9-데이터-모델-핵심">9. 데이터 모델 핵심</h2>
<ul>
<li><code>users</code>: 회원</li>
<li><code>posts</code>: 게시글/문의</li>
<li><code>comments</code>: 댓글</li>
<li><code>guest_posts</code>: 비로그인 문의</li>
<li><code>post_histories</code>: 게시글 작업 이력</li>
<li><code>login_logs</code>: 로그인 시도 로그</li>
<li><code>mail_logs</code>: 메일 발송/수신 로그</li>
<li><code>web_push_subscriptions</code>: 디바이스별 웹 푸시 구독</li>
<li><code>web_push_messages</code>: 푸시 발송/클릭/성공여부 이력</li>
<li><code>jobs</code>, <code>failed_jobs</code>, <code>sessions</code>, <code>password_reset_tokens</code>: 큐/세션/복구</li>
</ul>
<h2 id="readme-10-로컬-실행">10. 로컬 실행</h2>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve</code></pre></div>
<p>큐 사용 시:</p>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>php artisan queue:work</code></pre></div>
<p>프론트 자산 개발:</p>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>npm install
npm run dev</code></pre></div>
<p>프론트 자산(운영 배포용 빌드):</p>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>npm install
npm run build</code></pre></div>
<h2 id="readme-11-docker-실행-개요">11. Docker 실행 개요</h2>
<p><code>docker-compose.yml</code> 기준 서비스:</p>
<ul>
<li><code>app</code> (php-fpm)</li>
<li><code>queue</code> (queue worker)</li>
<li><code>nginx</code></li>
<li><code>mysql</code></li>
<li><code>node</code> (vite dev server)</li>
</ul>
<h2 id="readme-12-인프라-구축-핵심">12. 인프라 구축 핵심</h2>
<p>아래는 인프라 문서에서 <strong>개발환경 구축에 직접 필요한 내용만</strong> 압축한 체크리스트입니다.</p>
<h3 id="readme-121-로컬-docker-개발환경">12.1 로컬 Docker 개발환경</h3>
<p>목표: 로컬에서 <code>app + nginx + mysql + node + queue</code>를 분리 실행</p>
<ol>
<li>준비: Docker Desktop 실행, <code>docker -v</code>, <code>docker compose version</code> 확인</li>
<li>환경 파일: <code>.env_docker</code> 사용, 핵심 <code>APP_ENV=docker</code>, DB는 <code>DB_HOST=mysql</code></li>
<li>부트스트랩 분기: <code>bootstrap/app.php</code>에서 Docker 환경 시 <code>.env_docker</code> 로드</li>
<li>경로 규칙: 프로젝트 마운트 경로 <code>/var/www</code>, nginx root <code>/var/www/public</code></li>
</ol>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>docker compose down -v
docker compose up -d --build
docker compose ps</code></pre></div>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>docker exec -it th-app php artisan key:generate --force
docker exec -it th-app php artisan migrate

# 슈퍼어드민 보정(환경별 택1)
docker exec -it th-app php artisan db:seed --class=EnvSuperAdminSeeder --force
# docker exec -it th-app php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
docker exec -it th-app php artisan db:seed --class=NoteMasterSeeder --force</code></pre></div>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>docker compose up -d
docker compose down
docker compose restart
docker compose ps</code></pre></div>
<p>접속 포트(기본 예시): Web <code>http://localhost:8080</code>, Vite <code>http://localhost:5173</code>, MySQL <code>127.0.0.1:3307</code></p>
<h3 id="readme-122-서버-기본-구성">12.2 서버 기본 구성</h3>
<ul>
<li>AWS Lightsail (Ubuntu 22.04 LTS), SSH Key 기반 접속</li>
<li>타임존 <code>Asia/Seoul</code> 통일, 배포 경로 <code>/var/www/th-study</code> 고정</li>
<li>Nginx 서버블록 + HTTPS(Let's Encrypt) 적용</li>
<li>Queue 워커 상시 실행(systemd)</li>
</ul>
<h3 id="readme-123-ssl">12.3 SSL</h3>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>sudo apt update
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d example.com -d www.example.com
sudo certbot renew --dry-run</code></pre></div>
<h3 id="readme-124-메모리-안정화">12.4 메모리 안정화</h3>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
free -h</code></pre></div>
<h3 id="readme-125-cicd-핵심">12.5 CI/CD 핵심</h3>
<ul>
<li><code>main</code> push 시 <code>.github/workflows/deploy.yml</code> 기준으로 코드 동기화, <code>composer install --no-dev</code>, <code>php artisan optimize:clear/config:cache/route:cache/view:cache</code>, <code>php artisan migrate --force</code>, <code>php artisan db:seed --class=NoteMasterSeeder --force</code>, 웹서버 reload(<code>php8.2-fpm</code>, <code>nginx</code>), <code>php artisan queue:restart</code> 수행</li>
<li>슈퍼어드민 계정은 환경별 시더로 보정: 운영 <code>AutoSuperAdminSeeder</code>, 개발/로컬 <code>EnvSuperAdminSeeder</code></li>
<li>노트 마스터 데이터는 <code>config/seeders/note.php</code>에서 관리하며, 시더는 <code>updateOrCreate</code> 기반 동기화</li>
<li>자동배포 스크립트에는 민감정보 하드코딩 금지, 환경변수/비밀 저장소 사용</li>
</ul>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code># 개발/로컬: .env(SUPERADMIN_EMAIL, SUPERADMIN_PASSWORD) 기준 슈퍼어드민 보정
php artisan db:seed --class=EnvSuperAdminSeeder --force

# 운영: SUPERADMIN_EMAIL 기준 슈퍼어드민 보정(비밀번호 랜덤 생성, 콘솔 출력)
php artisan db:seed --class=AutoSuperAdminSeeder --force

# 노트 코드/마스터 데이터 동기화(config/seeders/note.php 기준)
php artisan db:seed --class=NoteMasterSeeder --force</code></pre></div>
<h3 id="readme-126-db-백업복구-최소-운영안">12.6 DB 백업/복구 최소 운영안</h3>
<ul>
<li>풀백업(일 1회) + 증분(binlog, 시간 단위) + 14일 보관 후 자동삭제</li>
<li>복구: 최신 풀백업 복원 후 해당 시점 이후 binlog 순차 적용</li>
</ul>
<h3 id="readme-127-빠른-점검-명령어">12.7 빠른 점검 명령어</h3>
<div class="codeblock"><div class="codehdr"><span>bash</span></div><pre><code>php artisan about --only=environment,drivers
php artisan migrate:status
sudo systemctl status php8.2-fpm
sudo systemctl status nginx
sudo systemctl status th-study-queue</code></pre></div>
<h3 id="readme-128-운영-경로-기준">12.8 운영 경로 기준</h3>
<ul>
<li>앱 루트: <code>/var/www/th-study</code></li>
<li>Nginx 로그: <code>/var/log/nginx/access.log</code>, <code>/var/log/nginx/error.log</code></li>
<li>Laravel 로그: <code>/var/www/th-study/storage/logs</code></li>
<li>DB 풀백업: <code>/backup/mysql/full</code></li>
<li>DB 증분백업(binlog): <code>/backup/binlog</code></li>
</ul>
<h3 id="readme-129-github-조직관리-가이드">12.9 GitHub 조직관리 가이드</h3>
<ul>
<li>Organization 생성, 저장소 이전, 멤버/팀 권한(Read/Write/Admin) 분리</li>
<li>서버 연동은 HTTPS+PAT 또는 SSH 중 하나로 통일</li>
<li><code>main</code> 브랜치 보호(force push 금지/PR 리뷰) 권장</li>
<li>PAT/SSH 개인키를 README/코드/스크립트에 직접 기록하지 않기</li>
</ul>
<h2 id="readme-13-운영-보안-가이드">13. 운영 보안 가이드</h2>
<ul>
<li><code>.env</code> 및 민감한 키/토큰/패스워드는 저장소에 커밋하지 않습니다.</li>
<li>README에는 실제 운영 계정, 실제 도메인 내부정보, 비밀키를 기록하지 않습니다.</li>
<li>로컬 개발용 라우트(<code>_dev/*</code>)는 운영에서 노출되지 않도록 환경 정책을 유지합니다.</li>
<li>메일/로그는 운영 추적용으로만 활용하고 개인정보 최소 수집 원칙을 지킵니다.</li>
</ul>
<h2 id="readme-14-기능-확장-시-체크리스트">14. 기능 확장 시 체크리스트</h2>
<p>기능 추가 시 아래 순서로 보면 소스 탐색이 빠릅니다.</p>
<ol>
<li><code>routes/*</code>에서 엔드포인트 위치 확인</li>
<li>대응 <code>Controller</code> 확인</li>
<li><code>Request</code> 검증 규칙 확인</li>
<li><code>Service</code> 도메인 규칙 확인</li>
<li><code>Repository</code>/<code>Model</code> DB 처리 확인</li>
<li><code>Policy</code>/<code>Middleware</code> 접근 정책 확인</li>
<li><code>views/*</code> 화면 및 <code>emails/*</code> 템플릿 확인</li>
</ol>
<p>이 순서를 기준으로 보면 기능이 커져도 추적 경로가 안정적으로 유지됩니다.</p>
<h2 id="readme-15-홈인트로-퍼블리싱-가이드">15. 홈/인트로 퍼블리싱 가이드</h2>
<p>향후 퍼블리싱 업데이트 시 아래 기준을 유지하면 브랜드 일관성을 지키기 쉽습니다.</p>
<h3 id="readme-141-페이지-역할-분리">14.1 페이지 역할 분리</h3>
<ul>
<li><code>home</code>: 서비스 핵심 요약, 신뢰 요소, CTA, 문의 전환</li>
<li><code>intro</code>: 섹션 전환 기반 스토리텔링, 약력/철학/확장 방향 강조</li>
</ul>
<h3 id="readme-142-콘텐츠-구조-참고">14.2 콘텐츠 구조 참고</h3>
<ul>
<li><code>home</code> 주요 블록</li>
<li>Hero(브랜드 키메시지 + 주요 CTA)</li>
<li>About(프로젝트 존재 이유)</li>
<li>Highlights/Keywords(핵심 가치)</li>
<li>Slogan/Why(브랜드 톤 강화)</li>
<li>Stats/Roadmap(운영 관점 미래 계획)</li>
<li>Profile/Stack(신뢰 근거)</li>
<li>Contact Modal + Guest Inquiry(전환 지점)</li>
<li><code>intro</code> 주요 블록</li>
<li>Hero(타이핑 슬로건)</li>
<li>소개, 약력, 사회공헌, PHP, AI, 수익, 문의</li>
<li>우측 Dot Navigation + 섹션별 이미지/태그/액션 버튼</li>
</ul>
<h3 id="readme-143-스타일-시스템-참고">14.3 스타일 시스템 참고</h3>
<ul>
<li><code>public/css/intro/home.css</code></li>
<li>다크 톤 그라디언트 배경 + 강조 색상(<code>--accent</code>, <code>--accent2</code>)</li>
<li>카드/칩/버튼 컴포넌트 중심 구성</li>
<li><code>reveal</code> 클래스로 섹션 등장 연출</li>
<li><code>public/css/intro/intro.css</code></li>
<li>풀스크린 섹션 전환, Dot Nav, Hero 배경 패럴랙스</li>
<li>태그/액션 버튼/리스트의 일관된 시각 규칙</li>
<li>모바일/접근성(<code>prefers-reduced-motion</code>) 대응 포함</li>
</ul>
<h3 id="readme-144-인터랙션-구현-참고">14.4 인터랙션 구현 참고</h3>
<ul>
<li><code>public/js/intro/home.js</code></li>
<li>앵커 스무스 스크롤</li>
<li><code>IntersectionObserver</code> 기반 reveal 애니메이션</li>
<li><code>public/js/intro/intro.js</code></li>
<li>섹션 전환(휠/터치/키보드)</li>
<li>타이핑 효과(<code>나를 뛰어넘는 개발자</code>)</li>
<li>이미지/배경 패럴랙스</li>
<li>내장 SVG 아이콘 동적 주입</li>
</ul>
<h3 id="readme-145-브랜드-이미지자산-운영-가이드">14.5 브랜드 이미지/자산 운영 가이드</h3>
<ul>
<li>홈 핵심 이미지: <code>public/images/main_logo.png</code>, <code>public/images/extension_logo.png</code>, <code>public/images/intro_project_img.jpg</code></li>
<li>소개 섹션 이미지: <code>public/images/intro/*.avif</code></li>
<li>업데이트 권장 방식</li>
<li>새 이미지 추가 시 기존 명명 규칙(<code>intro/001.avif</code> 형태) 유지</li>
<li>텍스트 변경 시 슬로건/키워드/CTA를 함께 조정</li>
<li>섹션 삭제/추가 시 <code>home.js</code>, <code>intro.js</code>의 앵커/네비게이션 동작 동시 점검</li>
<li>문구는 &quot;성장 + 확장 + 운영&quot; 3축을 유지</li>
</ul>
<h2 id="readme-16-마무리-한-줄">16. 마무리 한 줄</h2>
<p>티에이치스터디그룹은 하나의 사이트가 아니라, 개발자로서의 성장 과정을 담는 플랫폼이다.</p>
<h2 id="readme-19-codex-스킬-문서-가이드">19. Codex 스킬 문서 가이드</h2>
<p>이 프로젝트의 스킬 문서는 "어떻게 구현할지"를 빠르게 전달하는 작업 지침서 역할을 합니다.</p>
<h3 id="readme-191-스킬-폴더-구조">19.1 스킬 폴더 구조</h3>
<p>Codex 기준 스킬은 보통 아래 구조를 가집니다.</p>
<pre><code>skill-name/
  SKILL.md            # 필수, 스킬 본문
  agents/openai.yaml  # 권장, UI 메타데이터
  scripts/*           # 선택, 반복 작업용 실행 스크립트
  references/*        # 선택, 필요할 때만 읽는 참고 문서
  assets/*            # 선택, 템플릿/이미지/출력 리소스
</code></pre>
<h3 id="readme-192-파일-종류와-역할">19.2 파일 종류와 역할</h3>
<ul>
<li><code>SKILL.md</code>: 필수 파일, 스킬 이름/설명/작업 절차/사용 조건을 적는 본문</li>
<li><code>agents/openai.yaml</code>: 권장 파일, UI 메타데이터 관리</li>
<li><code>scripts/*</code>: 선택 파일, 반복 작업 자동화</li>
<li><code>references/*</code>: 선택 파일, 긴 참고 문서 분리</li>
<li><code>assets/*</code>: 선택 파일, 템플릿/샘플/이미지 리소스 저장</li>
</ul>
<h3 id="readme-193-skillmd-작성-방법">19.3 <code>SKILL.md</code> 작성 방법</h3>
<ol>
<li>스킬 목적을 한 줄로 먼저 정합니다.</li>
<li>YAML frontmatter에 <code>name</code>, <code>description</code>를 적습니다.</li>
<li>본문에는 사용 시점, 작업 절차, 참고 자료, 스크립트 사용 위치를 적습니다.</li>
<li>긴 설명은 <code>references/</code>로, 반복 작업은 <code>scripts/</code>로 분리합니다.</li>
<li>스킬 폴더 안에는 별도 <code>README.md</code>를 만들지 않는 것을 기준으로 합니다.</li>
</ol>
<h3 id="readme-194-현재-저장소의-스킬-파일-종류와-역할">19.4 현재 저장소의 스킬 파일 종류와 역할</h3>
<ul>
<li><code>skill/게시판.md</code>: 도메인 규격형 스킬, 게시판 CRUD/권한/히스토리/로그/메일 규칙 정의</li>
<li><code>skill/노트.md</code>: 기능 명세형 스킬, 노트 메뉴 구조/라우팅/권한/썸네일/태그 규칙 정의</li>
<li><code>skill/백엔드 기초.md</code>: 공통 개발 기준 스킬, PHP/Laravel 계층 규칙 안내</li>
<li><code>skill/프론트엔드 기초.md</code>: 퍼블리싱 기준 스킬, Bootstrap 5 중심 작업 원칙 안내</li>
</ul>

      </div>
    </details>
  </div>
</section>

</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/intro/portfolio.js') }}" defer></script>
@endpush
