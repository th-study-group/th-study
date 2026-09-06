@extends('layouts.app')

@section('title', '포트포리오')
@section('og_description', '개발자성장플랫폼 티에이치스터디 포트폴리오')

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
            <li>도메인: 가비아 등록 후 Cloudflare 네임서버/DNS/Email Routing 운영</li>
            <li>검증: 로컬 환경 및 Docker Compose 이용하여 테스트</li>
            <li>백업: <code style="color:#fff;">/backup/mysql</code> + <code style="color:#fff;">/backup/laravel_files</code> 14일 보관</li>
            <li>수익화: 카카오 애드핏 광고 단위를 PC/모바일/슬림/정사각형으로 분리 운영</li>
            <li>학습 확장: Python 3 + FastAPI 로컬 API 기초 정리</li>
            <li>API 문서화: OpenAPI 속성 기반 Swagger UI에 MCP 조회 도구 10종의 요청·응답·JWT Bearer 인증 명세를 반영하고, 관리자 다중 접근 제어 구성</li>
            <li>분석: 유입 로그, 전환 로그, 일별 통계 집계 구조와 구글애널리틱스4 허용 경로/IP 제한 적용</li>
            <li>MongoDB: 비정형 데이터 및 Raw 데이터 정리 방향과 Windows/macOS/Ubuntu 설치 기준 문서화</li>
          </ul>
          <div class="fw-bold text-white mb-2">Tech Stack</div>
          <div class="d-flex flex-wrap gap-3 align-items-center icons">
            <i class="devicon-php-plain"></i><i class="devicon-laravel-original"></i><i class="devicon-mysql-original"></i>
            <i class="devicon-nginx-original"></i><i class="devicon-ubuntu-plain"></i><i class="devicon-docker-plain"></i><i class="devicon-bootstrap-plain"></i><i class="devicon-python-plain"></i><i class="devicon-fastapi-plain"></i>
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
        <p class="leadx mb-0">표, 흐름도, 운영 문서를 포함</p>
        <div class="container text-center mt-5">
          <x-adfit
              :unit="config('adfit.common.square.unit')"
              :width="config('adfit.common.square.width')"
              :height="config('adfit.common.square.height')" 
          />
        </div>
      </div>
      <div class="col-lg-8">
        <div class="box pad toc">
          <ul>
            <li><a href="#overview">1. 개요</a><span class="toc-dots"></span><span class="toc-desc">방향/슬로건</span></li>
            <li><a href="#versions">2. 버전</a><span class="toc-dots"></span><span class="toc-desc">Laravel/PHP/Node 등</span></li>
            <li><a href="#note-module">3. 블로그 서비스 모듈 구축</a><span class="toc-dots"></span><span class="toc-desc">CRUD 기반 설계/검증/권한</span></li>
            <li><a href="#flows">4. 핵심 흐름</a><span class="toc-dots"></span><span class="toc-desc">메일/배포</span></li>
            <li><a href="#pwa-push">5. PWA 설치/푸시</a><span class="toc-dots"></span><span class="toc-desc">허용/구독/캐시 대응</span></li>
            <li><a href="#seo">6. 검색엔진 최적화</a><span class="toc-dots"></span><span class="toc-desc">sitemap/robots 운영</span></li>
            <li><a href="#run">7. 실행</a><span class="toc-dots"></span><span class="toc-desc">로컬/큐</span></li>
            <li><a href="#deploy">8. 배포</a><span class="toc-dots"></span><span class="toc-desc">SSH + git pull</span></li>
            <li><a href="#infra">9. 운영 인프라</a><span class="toc-dots"></span><span class="toc-desc">Lightsail + Swap</span></li>
            <li><a href="#backup">10. DB / 파일 백업</a><span class="toc-dots"></span><span class="toc-desc">14일 정책</span></li>
            <li><a href="#docker">11. 개발 검증 Docker</a><span class="toc-dots"></span><span class="toc-desc">compose on/off</span></li>
            <li><a href="#queue-service">12. Queue 영구 실행 systemd</a><span class="toc-dots"></span><span class="toc-desc">서비스 등록</span></li>
            <li><a href="#agent-rules">13. 에이전트 작업 규칙</a><span class="toc-dots"></span><span class="toc-desc">AGENTS.md와 영역별 규칙</span></li>
            <li><a href="#mcp-oauth">14. MCP OAuth/JWT 연동</a><span class="toc-dots"></span><span class="toc-desc">ChatGPT tool 연결 구조</span></li>
            <li><a href="#fastapi-study">15. FastAPI 학습</a><span class="toc-dots"></span><span class="toc-desc">로컬 설치와 기본 API</span></li>
            <li><a href="#laravel-swagger">16. 라라벨 스웨거 API 문서</a><span class="toc-dots"></span><span class="toc-desc">OpenAPI 자동 문서화</span></li>
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
        <div class="text-center my-3 d-block d-md-none">
          <x-adfit
              :unit="config('adfit.mobile.slim.unit')"
              :width="config('adfit.mobile.slim.width')"
              :height="config('adfit.mobile.slim.height')" />
        </div>

        <div class="text-center my-3 d-none d-md-block">
          <x-adfit
            :unit="config('adfit.pc.slim.unit')"
            :width="config('adfit.pc.slim.width')"
            :height="config('adfit.pc.slim.height')" />
        </div>

        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:28%">항목</th><th>버전</th></tr></thead>
          <tbody>
            <tr><td class='fw-bold'>Laravel</td><td>12</td></tr>
            <tr><td class='fw-bold'>PHP</td><td>8.2</td></tr>
            <tr><td class='fw-bold'>MySQL</td><td>8.0.45</td></tr>
            <tr><td class='fw-bold'>MongoDB</td><td>8.3.3 기준 문서화, macOS는 Homebrew 기반 8.3.x 패치 차이까지 정리</td></tr>
            <tr><td class='fw-bold'>Ubuntu</td><td>Ubuntu 22.04</td></tr>
            <tr><td class='fw-bold'>Nginx</td><td>1.18.0</td></tr>
            <tr><td class='fw-bold'>Node.js</td><td>20.20.0</td></tr>
            <tr><td class='fw-bold'>Vite</td><td>5.4.21</td></tr>
            <tr><td class='fw-bold'>Bootstrap</td><td>5</td></tr>
            <tr><td class='fw-bold'>Docker</td><td>27.5.1</td></tr>
            <tr><td class='fw-bold'>Python</td><td>3.x</td></tr>
            <tr><td class='fw-bold'>FastAPI</td><td>학습용 로컬 API 구성</td></tr>
            <tr>
              <td class="fw-bold">OG 이미지 확장</td>
              <td>공유용 OG 이미지를 큐 작업으로 분리하고, 사진 방향 보정 관련 PHP <code>gd</code>, <code>exif</code> 확장 확인</td>
            </tr>
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
      <div class="callout mt-4">
        <strong>MongoDB 활용 방향</strong><br>
        TH-STUDY에서는 핵심 서비스 데이터는 MySQL 중심으로 유지하고, MongoDB는 비정형 데이터와 Raw 데이터 정리 용도로 확장할 예정입니다.
        <br>
        우선 대상은 접근 로그, 봇 로그, AI 관련 데이터, 채팅형 데이터처럼 구조가 자주 바뀌거나 수집 중심인 영역입니다.
        <br>
        로컬 문서도 Windows 전용에서 끝내지 않고 macOS 기준까지 보완했으며, macOS는 Homebrew 기반 설치와 서비스 관리, 패치 버전이 8.3.x 범위에서 달라질 수 있다는 점을 핵심 차이로 정리했습니다.
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
              <td class="fw-bold">썸네일/OG 처리</td>
              <td>일반 썸네일은 즉시 저장하고, 공유용 OG 이미지는 <code>NoteImageProcessingJob</code>으로 분리해 <code>afterCommit()</code> 후 비동기 생성. Job 내부에서 썸네일 원본 경로를 재검사해 stale 작업을 차단하고 성공 시에만 기존 OG 파일 정리</td>
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
              <td class="fw-bold">홈 최신글 캐시</td>
              <td><code>ContentCacheService</code>로 공개 블로그 최신 5건을 60분 캐시. <code>content:blog:home:public:v{version}:limit:5</code> 버전형 키를 사용하며, 글 등록·수정·삭제·공개 전환이 커밋된 뒤 버전을 올려 다음 홈 요청에서 최신 데이터를 생성</td>
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
              <td>블로그 글 등록 시 이벤트 기반으로 <code>note_histories</code> 기록(작업구분, IP, UA, referer 포함). 프록시 환경에서는 <code>RequestIp</code> 기준(<code>CF-Connecting-IP</code> -&gt; <code>X-Forwarded-For</code> -&gt; <code>X-Real-IP</code>)으로 실클라이언트 IP를 저장</td>
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
          <li>OG 이미지는 트랜잭션 커밋 후 큐에서 생성하도록 분리해 등록/수정 응답과 이미지 가공을 분리</li>
        </ul>
      </div>
      <div class="callout mt-4">
        <strong>Toast UI Editor 링크 삽입 운영 기준</strong>
        <ul class="mb-0 mt-2">
          <li>Toast UI Editor에서 링크를 저장하면 단독 상세와 목록 팝업 상세에 같은 링크 스타일이 자동 반영되도록 구성했습니다.</li>
          <li>일반 문장 안 링크는 보라색 텍스트 링크로 보이고, 문단에 링크 하나만 있으면 pill 버튼처럼 표시됩니다.</li>
          <li>외부 사이트 링크는 새 창 이동 기준으로 보여 주고, 링크 뒤에 <code>↗</code> 아이콘이 함께 붙습니다.</li>
          <li>Markdown 탭 예시: <code>[예시링크](https://example.com)</code></li>
          <li>Markdown에서 버튼형 링크를 원하면 링크만 단독 문단으로 입력합니다.</li>
          <li>WYSIWYG 탭에서는 텍스트 선택 후 상단 링크 아이콘을 눌러 URL을 넣으면 됩니다.</li>
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
            <tr>
              <td class="fw-bold">7. VAPID / 문의 메일 주소</td>
              <td>공개 주소 <code>admin@th-study.com</code>은 Cloudflare Email Routing으로 예시 전달 주소에 포워딩하고, 웹에서는 <code>mailto:</code> 링크와 VAPID subject 식별자로 사용</td>
              <td><code>config/services.php</code>, <code>resources/views/intro.blade.php</code>, Cloudflare Email Routing</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="callout mt-4">
        <strong>운영 메일 구조</strong><br>
        도메인은 가비아에서 등록하고, 네임서버는 Cloudflare(<code>earl.ns.cloudflare.com</code>, <code>maeve.ns.cloudflare.com</code>)로 위임했습니다.
        <br>
        <code>admin@th-study.com</code> 으로 들어온 메일은 Cloudflare Email Routing을 통해 예시 전달 주소 <code>inbox@example.com</code> 으로 전달되는 구조입니다.
        <br>
        Laravel 설정에서는 <code>VAPID_SUBJECT=admin@th-study.com</code> 값을 사용하고, <code>config/services.php</code>에서 최종적으로 <code>mailto:admin@th-study.com</code> 형태로 조립합니다.
      </div>
    </div>
  </div>
</section>

<section id="seo" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">6. 검색엔진 최적화</h2>
    <div class="box pad">
      <p class="leadx mb-3">공개 페이지가 검색엔진에 안정적으로 수집되도록 sitemap, robots, 웹마스터 인증 메타 코드를 정적 메모가 아니라 코드 기준으로 관리합니다.</p>
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:22%">영역</th>
              <th>구현 내용</th>
              <th style="width:32%">기준 코드</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold">Sitemap 생성</td>
              <td><code>spatie/laravel-sitemap</code> 기반으로 `/sitemap.xml` 요청 시 XML 생성. 정적 URL은 설정에서, 공개 블로그 상세 URL은 DB에서 조립하며 <code>lastmod</code>는 수정일 또는 등록일을 사용</td>
              <td><code>app/Http/Controllers/SitemapController.php</code>, <code>app/Services/SitemapService.php</code>, <code>app/Repositories/NoteRepository.php</code>, <code>config/sitemap.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">robots.txt 운영</td>
              <td>정적 <code>public/robots.txt</code>를 제거하고 라우트 기반 동적 응답으로 전환. 크롤링 차단 경로와 sitemap 위치를 뷰에서 관리</td>
              <td><code>routes/web.php</code>, <code>resources/views/robots.blade.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">공개 URL 범위</td>
              <td>메인, 소개, 공지 목록, 블로그 전체/활성 카테고리, 포트폴리오와 공개 블로그 상세 URL을 sitemap 대상에 포함</td>
              <td><code>config/sitemap.php</code>, <code>NoteRepository::getSitemapBlogs()</code></td>
            </tr>
            <tr>
              <td class="fw-bold">Sitemap 캐시</td>
              <td>sitemap XML은 공개 <code>blog</code> 캐시 버전을 사용해 24시간 저장. 블로그 등록·수정·삭제·공개 전환이 커밋되면 버전을 올려 다음 요청에서 최신 URL과 수정일 기준으로 다시 생성</td>
              <td><code>app/Services/SitemapService.php</code>, <code>app/Services/ContentCacheService.php</code>, <code>app/Services/NoteService.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">차단 정책</td>
              <td><code>/admin</code>, <code>/dashboard</code>, <code>/users</code>, <code>/inquiries</code>, <code>/push</code>, 비밀번호/계정 복구 관련 경로는 robots에서 비노출 처리</td>
              <td><code>resources/views/robots.blade.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">카테고리 표현 정리</td>
              <td>사용자 노출 명칭을 기존 <code>음식</code>에서 <code>맛집</code>으로 통일해 블로그 카테고리 의미와 검색 표현을 맞춤</td>
              <td><code>config/note.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">네이버 서치어드바이저</td>
              <td>네이버 서치어드바이저에 사이트를 등록해 국내 검색엔진 수집 경로를 추가하고, 운영 기준 URL은 <code>/robots.txt</code>와 <code>/sitemap.xml</code>로 통일</td>
              <td>네이버 서치어드바이저, <code>routes/web.php</code>, <code>config/sitemap.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">웹마스터 인증 코드</td>
              <td>소유권 확인용 메타 코드를 공통 레이아웃 <code>&lt;head&gt;</code>에 두어 전체 페이지에 일관되게 반영하고, Git 이력으로 변경 내역을 추적</td>
              <td><code>resources/views/layouts/app.blade.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">ads.txt 운영</td>
              <td>애드센스 크롤러 인증용 <code>ads.txt</code>를 공개 루트(<code>/ads.txt</code>)로 노출해 검색/광고 검증 경로를 운영 코드와 함께 관리</td>
              <td><code>public/ads.txt</code></td>
            </tr>
            <tr>
              <td class="fw-bold">카카오 애드핏 운영</td>
              <td><code>config/adfit.php</code>에서 광고 단위를 분리하고 <code>&lt;x-adfit&gt;</code> 공통 컴포넌트로 블로그, 메인, 소개, 공지, 포트폴리오에 같은 기준으로 적용. 동적 화면은 광고 스크립트를 재호출해 노출 안정성을 맞춤</td>
              <td><code>config/adfit.php</code>, <code>resources/views/components/adfit.blade.php</code>, <code>resources/views/partials/head-scripts.blade.php</code></td>
            </tr>
            <tr>
              <td class="fw-bold">내부 유입 구조</td>
              <td>외부 유입 점검(네이버/구글)과 별도로 내부 유입 원천은 사용자/봇 raw 로그로 분리 저장하고, 전환 raw 로그는 <code>conversion_logs</code>로 별도 저장. 블로그 외부 링크는 <code>/outbound</code> 경유로 전환을 기록하며, 일 집계는 페이지/디바이스 기준으로 <code>conversion_count</code>까지 누적. <code>admin</code> 계정은 공통 가드로 유입/전환 수집에서 제외</td>
              <td><code>app/Http/Middleware/TrackAccessLog.php</code>, <code>app/Services/TrafficAnalyticsService.php</code>, <code>app/Support/TrafficTrackingGuard.php</code>, <code>app/Repositories/TrafficLogRepository.php</code>, <code>app/Repositories/TrafficStatRepository.php</code></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="callout mt-4">
        <strong>운영 기준</strong>
        <ul class="mb-0 mt-2">
          <li><code>APP_URL</code>이 sitemap/robots 절대 URL의 기준이므로 운영 도메인 값이 정확해야 함</li>
          <li>새 공개 페이지를 만들면 라우트 추가만으로 끝내지 않고 <code>config/sitemap.php</code>와 robots 정책도 함께 검토</li>
          <li>정적 sitemap 설정만 변경하는 배포는 sitemap 캐시가 최대 24시간 남을 수 있으므로, 관련 캐시 무효화 또는 재생성 시점을 함께 확인</li>
          <li>검색 유입 관리는 Google 색인만 보지 않고 네이버 서치어드바이저 수집 상태도 함께 확인</li>
          <li>광고 운영은 <code>config/adfit.php</code> 단위 구성과 <code>&lt;x-adfit&gt;</code> 컴포넌트 기준으로 통일하고, 공통 스크립트는 head에서 한 번만 로드</li>
          <li>내부 유입 데이터는 <code>access_logs/bot_access_logs</code> raw, 전환 데이터는 <code>conversion_logs</code> raw로 분리하고, 집계는 <code>daily_page_stats(conversion_count 포함)</code>를 기준으로 조회/확장(월/연 단위)</li>
          <li><code>user.level=admin</code>은 <code>TrafficTrackingGuard</code> 기준으로 유입/전환 로그를 모두 스킵</li>
          <li>로그 정리(<code>logs:cleanup</code>)는 매일 실행하며 초기 운영 단계 분석을 위해 <code>access_logs</code> 365일, <code>bot_access_logs</code> 365일, <code>conversion_logs</code> 500일 기준으로 보관 후 삭제</li>
          <li>전환 타입은 <code>traffic.conversion_types</code> 기준으로 FormRequest + Service 이중 검증으로 통일</li>
          <li>검색엔진 노출은 동적 페이지보다 공개 목록/브랜드 소개/포트폴리오 중심으로 우선 관리</li>
          <li>웹마스터 인증 메타 코드는 공통 레이아웃에 두고 버전 관리해, 페이지별 누락 없이 운영 변경 이력을 남김</li>
        </ul>
      </div>
      <div class="callout mt-4">
        <strong>유입/전환 분석 핵심</strong><br>
        사용자 유입은 <code>access_logs</code>, 봇 유입은 <code>bot_access_logs</code>, 전환 이벤트는 <code>conversion_logs</code>로 분리 저장합니다.
        <br>
        일별 집계는 <code>daily_page_stats</code>에 누적하며, 페이지/디바이스 기준으로 <code>total_access_count</code>, <code>real_access_count</code>, <code>conversion_count</code>를 함께 관리합니다.
        <br>
        MCP에서는 관리자 전용 <code>access_log_search</code>, <code>bot_access_log_search</code>, <code>conversion_log_search</code>, <code>daily_page_stat_search</code> tool로 사람 유입, 봇 유입, 전환 로그, 일별 집계를 다시 조회할 수 있게 연결했습니다.
        <br>
        초기에는 로그를 빨리 지우는 것보다 데이터 분석 기반을 만드는 쪽이 더 중요하다고 판단해 보존 주기를 늘렸고, 이후 트래픽 규모에 맞춰 다시 조정할 수 있게 설계했습니다.
      </div>
    </div>
  </div>
</section>

<section id="run" class="section">
  <div class="container">
    <h2 class="h2x mb-3">7. 실행</h2>
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
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 라라벨 크론탭 등록(통계)</span><button class="copybtn no-print" onclick="copyFrom('#scheduleCron', this)">복사</button></div>
      <pre id="scheduleCron"><code># Ubuntu 기준
crontab -e

# 1분마다 스케줄 러너 실행
* * * * * cd /var/www/th-study && php artisan schedule:run >> /dev/null 2>&1</code></pre>
    </div>
    </div>
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 통계 수동 실행</span><button class="copybtn no-print" onclick="copyFrom('#statsManual', this)">복사</button></div>
      <pre id="statsManual"><code># 오늘 집계
php artisan stats:aggregate-daily

# 전날 집계 (Ubuntu)
php artisan stats:aggregate-daily $(date -d "yesterday" +%F)

# 전날 집계 (직접 입력 예시: YYYY-MM-DD 권장)
php artisan stats:aggregate-daily 2026-03-01

# 스케줄 트리거 수동 실행
php artisan schedule:run</code></pre>
    </div>
    <div class="small text-muted mt-2">
      <code>stats:aggregate-daily</code>는 <code>access_logs</code>와 <code>conversion_logs</code>를 병합해 <code>daily_page_stats.conversion_count</code>까지 반영합니다.
    </div>
    </div>
    <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 서버 크론 로그 확인</span><button class="copybtn no-print" onclick="copyFrom('#cronLogs', this)">복사</button></div>
      <pre id="cronLogs"><code># cron 데몬 로그 실시간
sudo journalctl -u cron -f

# syslog CRON 실행 이력
sudo grep CRON /var/log/syslog | tail -n 100

# Laravel 앱 로그
tail -f /var/www/th-study/storage/logs/app.log
# 또는
tail -f /var/www/th-study/storage/logs/laravel.log

# 스케줄 커맨드 출력 로그(일자별)
tail -f /var/www/th-study/storage/logs/schedule-stats-$(date +%F).log
tail -f /var/www/th-study/storage/logs/schedule-logs-cleanup-$(date +%F).log</code></pre>
    </div>
    </div>
    <div class="callout mt-4">
      <strong>스케줄 출력 파일 정책</strong><br>
      <ul class="mb-0" style="margin-left:18px;">
        <li><code>stats:aggregate-daily</code> 출력: <code>storage/logs/schedule-stats-YYYY-MM-DD.log</code></li>
        <li><code>logs:cleanup</code> 출력: <code>storage/logs/schedule-logs-cleanup-YYYY-MM-DD.log</code></li>
        <li>앱 로그 파일은 서버 설정에 따라 <code>app.log</code> 또는 <code>laravel.log</code>를 사용</li>
        <li>날짜 인자는 <code>YYYY-MM-DD</code> 형식 권장 (예: <code>2026-03-01</code>)</li>
      </ul>
    </div>
    </div>
  </div>
</section>

<section id="deploy" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">8. 배포</h2>
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
    <h2 class="h2x mb-3">9. 운영 인프라 AWS Lightsail</h2>
    <div class="box pad">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:22%">항목</th><th>값</th></tr></thead>
          <tbody>
            <tr><td class="fw-bold">서버</td><td>AWS Lightsail / Ubuntu</td></tr>
            <tr><td class="fw-bold">웹</td><td>Nginx + PHP-FPM</td></tr>
            <tr><td class="fw-bold">DB</td><td>MySQL</td></tr>
            <tr><td class="fw-bold">도메인/DNS</td><td>가비아 등록 도메인 + Cloudflare 네임서버/DNS 운영 (<code>earl.ns.cloudflare.com</code>, <code>maeve.ns.cloudflare.com</code>)</td></tr>
            <tr><td class="fw-bold">대표 메일</td><td><code>admin@th-study.com</code> -> Cloudflare Email Routing -> <code>inbox@example.com</code> (예시)</td></tr>
            <tr><td class="fw-bold">실클라이언트 IP</td><td><code>TrustProxies</code> + <code>RequestIp</code> 적용으로 로그인/게시판/히스토리에 프록시 IP가 아닌 사용자 IP 저장</td></tr>
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
    <h2 class="h2x mb-3">10. DB / 파일 백업</h2>
    <div class="box pad">
      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead><tr><th style="width:18%">구분</th><th>경로</th><th style="width:22%">주기</th><th style="width:22%">보관</th></tr></thead>
          <tbody>
            <tr><td class="fw-bold">Full</td><td><code>/backup/mysql/full</code></td><td>1일 1회</td><td>14일</td></tr>
            <tr><td class="fw-bold">Binlog</td><td><code>/backup/mysql/binlog</code></td><td>매시간</td><td>14일</td></tr>
            <tr><td class="fw-bold">Files</td><td><code>/backup/laravel_files</code></td><td>매일 03:30</td><td>14일</td></tr>
          </tbody>
        </table>
      </div>
      <div class="callout mt-4">
        <strong>파일 백업 기준</strong><br>
        <ul class="mb-0" style="margin-left:18px;">
          <li>대상: <code>/var/www/th-study/storage/app/public</code> 전체</li>
          <li>백업 스크립트: <code>/usr/local/bin/laravel_file_backup.sh</code></li>
          <li>정리 스크립트: <code>/usr/local/bin/laravel_file_backup_cleanup.sh</code></li>
          <li>root 소유 백업은 필요 시 <code>/home/ubuntu</code>에 <code>ubuntu</code> 소유 사본을 만들어 FileZilla(SFTP)로 내려받음</li>
        </ul>
      </div>
      <div class="mt-4">
        <div class="codeblock">
          <div class="codehdr"><span>bash · 라라벨 업로드 백업 스크립트</span><button class="copybtn no-print" onclick="copyFrom('#fileBackupScript', this)">복사</button></div>
          <pre id="fileBackupScript"><code>#!/usr/bin/env bash
set -e

PROJECT_DIR="/var/www/th-study"
UPLOAD_DIR="$PROJECT_DIR/storage/app/public"
BACKUP_DIR="/backup/laravel_files"
NOW="$(date +%F_%H-%M-%S)"

mkdir -p "$BACKUP_DIR"

tar -czf "$BACKUP_DIR/laravel_${NOW}.tar.gz" \
  -C "$UPLOAD_DIR" .</code></pre>
        </div>
      </div>
      <div class="mt-4">
        <div class="codeblock">
          <div class="codehdr"><span>bash · 파일 백업 정리 + 테스트</span><button class="copybtn no-print" onclick="copyFrom('#fileBackupOps', this)">복사</button></div>
          <pre id="fileBackupOps"><code># 14일 지난 파일 백업 삭제
find /backup/laravel_files -name "laravel_*.tar.gz" -mtime +14 -delete

# 파일 백업 실행 확인 스크립트:
sudo grep CRON /var/log/syslog | grep laravel_file_backup

# 실행 테스트
sudo /usr/local/bin/laravel_file_backup.sh
sudo sh /usr/local/bin/laravel_file_backup.sh
sudo /usr/local/bin/laravel_file_backup_cleanup.sh
sudo sh /usr/local/bin/laravel_file_backup_cleanup.sh

# 결과 확인
ls -lh /backup/laravel_files</code></pre>
        </div>
      </div>
      <div class="mt-4">
        <div class="codeblock">
          <div class="codehdr"><span>bash · root crontab</span><button class="copybtn no-print" onclick="copyFrom('#fileBackupCron', this)">복사</button></div>
          <pre id="fileBackupCron"><code># 매일 03:30 라라벨 업로드 전체 백업
30 3 * * * /usr/local/bin/laravel_file_backup.sh

# 매일 04:50 라라벨 업로드 백업 중 14일 지난 파일 삭제
50 4 * * * /usr/local/bin/laravel_file_backup_cleanup.sh</code></pre>
        </div>
      </div>
      <div class="callout mt-4">
        <strong>PWA 에러 화면 커스텀</strong><br>
        <ul class="mb-0" style="margin-left:18px;">
          <li><code>404</code>, <code>419</code>, <code>429</code>, <code>500</code>, <code>503</code> 공통 에러 화면 구성</li>
          <li>상태코드 숫자 크게 표시 + 한글 설명 + <code>메인으로 가기</code> 버튼 제공</li>
          <li>예외 분기: <code>app/Exceptions/Handler.php</code>, 공통 뷰: <code>resources/views/errors/minimal.blade.php</code></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section id="docker" class="section">
  <div class="container">
    <h2 class="h2x mb-3">11. 개발 검증 Docker</h2>
    <div class="box pad">
      <p class="leadx mb-2">배포 전 동일한 Ubuntu 기반 환경을 검증하기 위해 Docker Compose를 사용합니다.</p>
      <div class="callout mt-3">
        <strong>Docker 접속 포트</strong><br>
        Web은 <code>localhost:8080</code>, Vite는 <code>localhost:5173</code>, MySQL은 <code>127.0.0.1:3307</code>으로 접속합니다.
        <br>
        MongoDB는 Docker 호스트 기준 <code>127.0.0.1:27018</code>로 접속하고, 컨테이너 내부 포트는 <code>27017</code>입니다.
        <br>
        MongoDB Docker 이미지는 <code>mongo:8.3.3</code>이며, 루트 계정은 <code>root</code>, 인증 데이터베이스는 <code>admin</code>입니다.
      </div>
      
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
      <div class="mt-4">
    <div class="codeblock">
      <div class="codehdr"><span>bash · 도커 MongoDB 접속(예시)</span><button class="copybtn no-print" onclick="copyFrom('#dcMongo', this)">복사</button></div>
      <pre id="dcMongo"><code># 컨테이너 내부 접속
docker exec -it th-mongodb mongosh -u [사용자명] -p '[비밀번호]' --authenticationDatabase admin

# 호스트에서 접속
mongosh "mongodb://[사용자명]:[비밀번호]@127.0.0.1:27018/admin?authSource=admin"</code></pre>
    </div>
    </div>
    </div>
  </div>
</section>

<section id="queue-service" class="section">
  <div class="container">
    <h2 class="h2x mb-3">12. Queue 영구 실행 systemd</h2>
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

<section id="agent-rules" class="section">
  <div class="container">
    <h2 class="h2x mb-3">13. 에이전트 작업 규칙</h2>
    <div class="box pad">
      <p class="leadx mb-3">
        프로젝트 루트의 <code>AGENTS.md</code>는 에이전트가 작업 전에 확인하는 공통 안내 문서입니다.
        요청에 맞는 <code>agent_rules/</code> 규칙을 함께 확인해 일관된 방식으로 작업합니다.
      </p>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">파일</th>
              <th>역할</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold"><code>AGENTS.md</code></td>
              <td>공통 작업 절차, 규칙 파일 선택 기준, 사용자 확인 및 작업 완료 보고 기준을 안내</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="callout mt-4">
        <strong>작업 규칙 적용 순서</strong>
        <ul class="mb-0 mt-2">
          <li>루트 <code>AGENTS.md</code>를 먼저 확인합니다.</li>
          <li>요청 범위에 맞는 <code>agent_rules/*.md</code> 파일을 읽습니다.</li>
          <li>여러 영역에 걸친 작업은 관련 규칙을 모두 적용합니다.</li>
          <li>기능별 규칙과 공통 규칙이 함께 있으면 기능별 규칙을 우선합니다.</li>
          <li>공통 규칙은 실제 Migration, 코드, Blade/CSS/JS에서 확인한 패턴을 기준으로 관리하며, 혼재된 구현을 임의로 통일하지 않습니다.</li>
        </ul>
      </div>

      <div class="callout mt-4">
        <strong>공통 규칙 범위</strong><br>
        <code>backend.md</code>에는 Route/Middleware, 계층 분리, 검증·권한, DB/Model, 트랜잭션·이력·Queue 흐름을 정리하고,
        <code>frontend.md</code>에는 Blade/Layout, Bootstrap 5, 공통 CSS/JS, 목록·폼·반응형 UI, jQuery/AJAX 패턴을 정리합니다.
        기능 전용 정책은 <code>board.md</code>, <code>note.md</code>에 유지합니다.
      </div>

      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">현재 규칙 파일</th>
              <th>종류 / 역할</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold"><code>agent_rules/board.md</code></td>
              <td>게시판 CRUD, 권한, 히스토리, 로그, 페이징, 메일 규칙 정의</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>agent_rules/note.md</code></td>
              <td>노트 메뉴 구조, 라우팅, 권한, 썸네일, 해시태그, 히스토리 규칙 정의</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>agent_rules/backend.md</code></td>
              <td>Route/Middleware, Controller-Service-Repository, FormRequest/Policy, DB·Model, 트랜잭션·이력·Queue 공통 규칙</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>agent_rules/frontend.md</code></td>
              <td>Blade/Layout, Bootstrap 5, 공통 CSS/JS, 목록·폼·반응형 UI, jQuery/AJAX 공통 규칙</td>
            </tr>
            <tr>
              <td class="fw-bold"><code>agent_rules/mcp.md</code></td>
              <td>MCP Tool/API, OAuth/JWT 인증, Tool 정의, 권한, Validation, Logging, Pagination 규칙</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section id="mcp-oauth" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">14. MCP OAuth/JWT 연동</h2>
    <div class="box pad">
      <p class="leadx mb-3">
        ChatGPT 같은 MCP 클라이언트가 TH-Study 안의 데이터를 안전하게 조회할 수 있도록
        OAuth authorization code 흐름, JWT access/refresh token, MCP API, tool dispatch 구조를 직접 연결했습니다.
      </p>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">핵심 구현 포인트</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li><code>/mcp/oauth/authorize</code> 에서 MCP 로그인 화면 제공</li>
              <li><code>/api/mcp/oauth/token</code> 에서 authorization code / refresh token 교환 처리</li>
              <li>OpenAI Apps 심사 기준에 맞춰 OAuth pre-defined 등록과 <code>thstudy-chatgpt</code> client_id 구성</li>
              <li>ChatGPT 반영 버전 <code>v1.0.0</code> 기준으로 MCP 심사 통과</li>
              <li><code>mcp_jwt</code> 가드와 전용 미들웨어로 MCP 보호 API 분리</li>
              <li>이메일 인증을 완료하고 <code>api_access_status=approved</code>로 승인된 사용자만 API를 통해 데이터를 조회하도록 이중 접근 제어</li>
              <li>API 승인 시각은 <code>api_access_approved_datetime</code>에 기록해 승인 이력을 관리</li>
              <li><code>/.well-known/*</code> 메타데이터를 노출해 클라이언트가 인증 서버 정보를 찾도록 구성</li>
              <li><code>mcp/tool.json</code> 기반으로 <code>tools/list</code>, <code>tools/call</code>을 처리하고 tool별 <code>levels</code>로 허용 계정 레벨 관리</li>
              <li>현재는 <code>note_group_search</code>, <code>note_category_search</code>, <code>note_topic_search</code>, <code>note_search</code>, <code>note_tag_search</code>, <code>user_search</code>, <code>access_log_search</code>, <code>bot_access_log_search</code>, <code>conversion_log_search</code>, <code>daily_page_stat_search</code>를 공개</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="codeblock">
            <div class="codehdr"><span>flow · MCP 인증/호출</span></div>
            <pre><code>ChatGPT
  -> /mcp/oauth/authorize
  -> /mcp/oauth/login
  -> /api/mcp/oauth/token
  -> Bearer access token 발급
  -> /api/mcp (initialize, tools/list, tools/call)
  -> /api/mcp/tools/note-groups
  -> /api/mcp/tools/note-categories
  -> /api/mcp/tools/note-topics
  -> /api/mcp/tools/notes
  -> /api/mcp/tools/note-tags
  -> /api/mcp/tools/users
  -> /api/mcp/tools/access-logs
  -> /api/mcp/tools/bot-access-logs
  -> /api/mcp/tools/conversion-logs
  -> /api/mcp/tools/daily-page-stat-logs</code></pre>
          </div>
        </div>
      </div>
      <div class="table-responsive mt-4">
        <table class="table table-bordered align-middle mb-0">
          <thead>
            <tr>
              <th style="width:24%">구성 요소</th>
              <th>정리</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="fw-bold">인증 진입점</td>
              <td><code>McpOAuthController</code>에서 <code>client_id</code>, <code>redirect_uri</code>, PKCE 파라미터를 검증하고 로그인 화면을 제공합니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">토큰 발급</td>
              <td>authorization code를 캐시에 짧게 저장한 뒤 access / refresh JWT를 발급하고, refresh token으로 access token 재발급까지 처리합니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">보호 API</td>
              <td><code>McpJwtAuthenticate</code>에서 Bearer 토큰 존재 여부, <code>token_type=access</code>, 이메일 인증 완료 여부와 <code>api_access_status=approved</code> 상태를 검증합니다. 승인 시각은 <code>api_access_approved_datetime</code>에 기록합니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">MCP 메서드</td>
              <td><code>McpApiController</code>에서 <code>initialize</code>, <code>tools/list</code>, <code>tools/call</code>을 JSON-RPC 형식으로 응답하고, tool 정의는 <code>mcp/tool.json</code> 기준으로 조회합니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">툴 라우팅</td>
              <td><code>ToolRunner</code>가 <code>mcp/tool.json</code> 정의를 읽고 로그인 계정의 <code>user.level</code>이 tool의 <code>levels</code>에 포함되는지 확인한 뒤, 허용된 경우에만 내부 서브 요청으로 개별 Laravel 컨트롤러/서비스에 연결합니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">현재 tool 구성</td>
              <td>노트 계열 5개 tool(<code>note_group_search</code>, <code>note_category_search</code>, <code>note_topic_search</code>, <code>note_search</code>, <code>note_tag_search</code>)은 <code>normal</code>, <code>admin</code> 계정에서 조회 가능하고, <code>user_search</code>, <code>access_log_search</code>, <code>bot_access_log_search</code>, <code>conversion_log_search</code>, <code>daily_page_stat_search</code>는 개인정보 또는 식별 가능 정보가 포함될 수 있어 <code>admin</code> 전용으로 분리했습니다.</td>
            </tr>
            <tr>
              <td class="fw-bold">심사 대응 포인트</td>
              <td>모든 MCP tool에 <code>readOnlyHint=true</code>, <code>openWorldHint=false</code>, <code>destructiveHint=false</code>를 적용했고, 도메인 인증과 심사용 read-only 계정도 별도로 준비했습니다.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-3 border rounded-4 mt-4">
        <div class="fw-bold mb-2">OpenAI Apps 심사 반영 핵심</div>
        <ul class="mb-0" style="margin-left:18px;">
          <li>OpenAI Apps MCP 심사를 통과했고 ChatGPT에 반영 완료되었습니다.</li>
          <li>현재 공개 버전은 <code>v1.0.0</code> 입니다.</li>
          <li><code>/.well-known/openai-apps-challenge</code> 경로로 도메인 인증을 통과</li>
          <li>MCP는 TH-Study 내부 데이터만 조회하며 생성, 수정, 삭제, 결제, 외부 인터넷 호출을 하지 않음</li>
          <li>심사용 계정은 이메일 인증이 완료된 <code>normal</code> 권한 read-only 계정으로 분리</li>
          <li>날씨, 계산, 일반 대화처럼 서비스와 무관한 질문에는 MCP tool을 호출하지 않도록 네거티브 테스트까지 정리</li>
        </ul>
      </div>
      <div class="callout mt-4">
        <strong>정리 포인트</strong><br>
        단순한 “AI 연결”이 아니라 인증 화면, 토큰 수명, 보호 리소스 메타데이터, 툴 정의 파일, 권한 레벨 분기, 내부 서비스 재사용 구조까지
        운영 가능한 형태로 묶고 실제 조회용 tool 세트까지 확장한 작업입니다.
      </div>
    </div>
  </div>
</section>

<section id="fastapi-study" class="section">
  <div class="container">
    <h2 class="h2x mb-3">15. FastAPI 학습</h2>
    <div class="box pad">
      <p class="leadx mb-3">FastAPI는 Python 기반 API 프레임워크 학습을 위해 별도 정리했습니다. Laravel/PHP 중심 개발 흐름에서 Python API 개발로 확장해 보면서, 향후 인공지능과 AI 학습 생태계에 접근할 수 있는 기반을 만드는 방향으로 학습하고 있습니다.</p>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">핵심 기준</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li><code>python3</code> 기준 실행</li>
              <li><code>python3 -m pip</code> 기준 설치</li>
              <li><code>venv</code> 기반 가상환경 분리</li>
              <li><code>FastAPI + Uvicorn</code> 기본 API 실행 학습</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="codeblock">
            <div class="codehdr"><span>python · main.py</span></div>
            <pre><code>from fastapi import FastAPI

app = FastAPI()

@app.get("/")
def home():
    return {"message": "hello 태희"}</code></pre>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-1">
        <div class="col-lg-6">
          <div class="codeblock">
            <div class="codehdr"><span>bash · 설치/실행</span></div>
            <pre><code>mkdir fastapi-study
cd fastapi-study
python3 -m venv .venv
source .venv/bin/activate
python3 -m pip install fastapi uvicorn
uvicorn main:app --reload</code></pre>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">학습 포인트</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li><code>http://127.0.0.1:8000</code> 에서 기본 응답 확인</li>
              <li><code>http://127.0.0.1:8000/docs</code> 에서 Swagger UI 자동 문서 확인</li>
              <li>라우트 추가 시 문서와 테스트 화면이 자동 반영되는 구조 학습</li>
              <li>Python API 경험을 바탕으로 AI/데이터 처리 확장 가능성 확보</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="callout mt-4">
        <strong>Swagger 자동 문서화</strong><br>
        FastAPI의 장점 중 하나는 API 문서 화면이 자동으로 연결된다는 점입니다.
        <br>
        <code>/docs</code> 경로에서 Swagger UI를 바로 확인할 수 있어서, 라우트 구조를 문서와 테스트 화면으로 함께 학습하기 좋습니다.
      </div>
      <div class="callout mt-4">
        <strong>정리 범위</strong><br>
        포트폴리오에는 학습 방향, 기본 소스, 설치/실행 흐름을 간단히 요약하고, 더 자세한 메모는 README에 별도로 정리했습니다.
      </div>
    </div>
  </div>
</section>

<section id="laravel-swagger" class="section bg-light">
  <div class="container">
    <h2 class="h2x mb-3">16. 라라벨 스웨거 API 문서</h2>
    <div class="box pad">
      <p class="leadx mb-3">Laravel API에는 <code>L5-Swagger 11</code>과 PHP OpenAPI 속성을 적용해, 구현 코드와 API 문서를 함께 관리하고 관리자만 안전하게 확인할 수 있도록 구성했습니다.</p>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">문서와 테스트 경로</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li><code>/api/documentation</code>: Swagger UI</li>
              <li><code>/docs</code>: 생성된 OpenAPI JSON</li>
              <li><code>/api/swagger-test</code>: JSON 응답 예시 API</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="codeblock h-100">
            <div class="codehdr"><span>bash · OpenAPI 명세 생성</span></div>
            <pre><code># local .env
L5_SWAGGER_ENABLED=true
L5_SWAGGER_GENERATE_ALWAYS=true

php artisan optimize:clear
php artisan l5-swagger:generate</code></pre>
          </div>
        </div>
      </div>
      <div class="callout mt-4">
        <strong>코드 기반 문서화</strong><br>
        <code>app/OpenApi/OpenApiSpec.php</code>에서 API 정보·서버·보안 스키마를 정의하고,
        각 컨트롤러의 OpenAPI 속성에서 경로와 응답 형식을 선언합니다. 생성 결과는
        <code>storage/api-docs/api-docs.json</code>에 저장되어 Swagger UI에서 확인하고 요청을 테스트할 수 있습니다.
        <code>swagger-test</code>에는 <code>bearerAuth</code>를 연결해 자물쇠·Authorize 버튼과 Bearer Token 전달도 확인합니다.
      </div>
      <div class="callout mt-4">
        <strong>MCP API Swagger 적용 완료</strong><br>
        MCP 개별 조회 도구 10종을 컨트롤러 OpenAPI 속성으로 문서화했습니다. 노트 조회용
        <code>note-groups</code>, <code>note-categories</code>, <code>note-topics</code>, <code>notes</code>, <code>note-tags</code>와
        관리자 조회용 <code>users</code>, <code>access-logs</code>, <code>bot-access-logs</code>, <code>conversion-logs</code>,
        <code>daily-page-stat-logs</code>는 모두 <code>POST /api/mcp/tools/*</code> 경로에서 JWT Bearer 인증, 검색 조건,
        페이지네이션, 성공 응답 및 <code>401</code>·<code>422</code> 응답을 Swagger UI로 확인할 수 있습니다.
      </div>
      <div class="row g-3 mt-1">
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">문서 접근 제어</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li><code>L5_SWAGGER_ENABLED=false</code>이면 문서 경로를 404로 숨김</li>
              <li>비로그인 접근은 401, 일반회원 접근은 403으로 차단</li>
              <li><code>SwaggerEnabled</code> 미들웨어를 UI·JSON·에셋 경로에 공통 적용</li>
              <li>관리자만 Laravel 단계의 문서 화면에 접근</li>
            </ul>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="p-3 border rounded-4 h-100">
            <div class="fw-bold mb-2">운영 보호 및 배포</div>
            <ul class="mb-0" style="margin-left:18px;">
              <li>Nginx Basic Auth를 <code>/api/documentation</code>, <code>/docs</code>에 추가 적용</li>
              <li>운영 기본값은 <code>ENABLED=false</code>, <code>GENERATE_ALWAYS=false</code></li>
              <li>명세 갱신 후 <code>config:cache</code>, <code>route:cache</code> 재생성</li>
              <li>태그 설명 중복은 <code>augmentTags.withDescription=false</code>로 방지</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/intro/portfolio.js') }}" defer></script>
@endpush
