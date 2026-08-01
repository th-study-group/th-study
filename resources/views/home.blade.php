@extends('layouts.app')

@section('title', '홈')

@section('style')
@endsection

@push('styles')
    <link href="{{ asset('css/intro/home.css') }}?v={{ filemtime(public_path('css/intro/home.css')) }}" rel="stylesheet" />
@endpush


@section('content')
<main class="col-12 p-0 home-page">
    <div class="home-landing">
        <header id="top" class="section-pad">
            <div class="container reveal">
                <div class="row align-items-center g-4 g-lg-5">
                    <div class="col-lg-6">
                        <span class="kicker mb-3">Developer Growth Archive</span>
                        <h1 class="display-4 fw-black mb-3">티에이치스터디</h1>
                        <h2 class="hero-subtitle mb-3">
                            <span id="heroTypingText" data-text="성장하는 개발자가 장인정신을 지닌 리더로 나아가는 과정"></span>
                        </h2>
                        <p class="lead mb-4 muted">
                            개발, 인프라, 운영, 실험을 기록하고 공유하는 성장 아카이브입니다.
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="chip">성장 기록</span>
                            <span class="chip">실험</span>
                            <span class="chip">운영</span>
                            <span class="chip">수익화</span>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a class="btn btn-accent btn-lg" href="#highlights">핵심 요약 보기</a>
                            <a class="btn btn-outline-dark btn-lg" href="{{ route('intro') }}">사이트 소개</a>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="hero-visual soft-card">
                            <div class="code-window-top"><span></span><span></span><span></span></div>
                            <img
                                src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80"
                                alt="개발자 코드 작업 이미지"
                                class="img-fluid hero-image"
                                loading="lazy">
                            <div class="code-preview">
<pre><code><span class="code-key">const</span> mission = {
  <span class="code-prop">build</span>: <span class="code-string">'th-study.com'</span>,
  <span class="code-prop">focus</span>: [<span class="code-string">'record'</span>, <span class="code-string">'experiment'</span>, <span class="code-string">'operate'</span>],
  <span class="code-prop">goal</span>: <span class="code-string">'sustainable growth'</span>
};

<span class="code-key">function</span> deploy() {
  <span class="code-key">return</span> <span class="code-string">'consistency > speed'</span>;
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>


        <section class="py-4">
            <div class="container text-center">
                <x-adfit
                    :unit="config('adfit.common.square.unit')"
                    :width="config('adfit.common.square.width')"
                    :height="config('adfit.common.square.height')" 
                />
            </div>
        </section>

        <section id="latest-blogs" class="section-pad anchor-offset">
            <div class="container reveal">
                <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-4">
                    <div>
                        <span class="kicker mb-2">Blogs</span>
                        <h2 class="fw-bold mb-1">최신 블로그 글</h2>
                        <p class="muted mb-0">최근에 올린 블로그 글을 한곳에 모았습니다.</p>
                    </div>

                    <a class="btn btn-outline-dark" href="{{ route('blogs.index') }}">전체 글 보기</a>
                </div>

                @if(!empty($latestBlogs))
                    {{-- PC / 태블릿: 현재처럼 최신글을 한눈에 표시합니다. --}}
                    <div class="row g-3 d-none d-md-flex">
                        @foreach($latestBlogs as $blog)
                            <div class="col-md-6 col-xl">
                                <article class="latest-blog-card soft-card h-100">
                                    <div class="latest-blog-meta">{{ $blog['category'] }} · {{ $blog['date'] }}</div>
                                    <h5 class="latest-blog-title">{{ $blog['title'] }}</h5>
                                    <p class="latest-blog-desc muted">{{ $blog['description'] }}</p>
                                    <a class="latest-blog-link" href="{{ $blog['show_url'] }}">자세히보기</a>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    {{-- 모바일: Bootstrap Carousel은 터치 스와이프와 순환 이동을 기본 지원합니다. --}}
                    <div id="latestBlogsCarousel"
                         class="carousel slide latest-blogs-carousel d-md-none"
                         data-bs-ride="false"
                         data-bs-touch="true"
                         data-bs-wrap="true"
                         data-bs-interval="false"
                         aria-label="최신 블로그 글">
                        <div class="carousel-indicators latest-blogs-indicators">
                            @foreach($latestBlogs as $index => $blog)
                                <button type="button"
                                        data-bs-target="#latestBlogsCarousel"
                                        data-bs-slide-to="{{ $index }}"
                                        class="{{ $index === 0 ? 'active' : '' }}"
                                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-label="{{ $index + 1 }}번째 최신 글"></button>
                            @endforeach
                        </div>

                        <div class="carousel-inner">
                            @foreach($latestBlogs as $index => $blog)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <article class="latest-blog-card soft-card">
                                        <div class="latest-blog-meta">{{ $blog['category'] }} · {{ $blog['date'] }}</div>
                                        <h5 class="latest-blog-title">{{ $blog['title'] }}</h5>
                                        <p class="latest-blog-desc muted">{{ $blog['description'] }}</p>
                                        <a class="latest-blog-link" href="{{ $blog['show_url'] }}">자세히보기</a>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        <div class="latest-blogs-carousel-controls">
                            <button class="carousel-control-prev latest-blogs-carousel-control"
                                    type="button"
                                    data-bs-target="#latestBlogsCarousel"
                                    data-bs-slide="prev"
                                    aria-label="이전 최신 글">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next latest-blogs-carousel-control"
                                    type="button"
                                    data-bs-target="#latestBlogsCarousel"
                                    data-bs-slide="next"
                                    aria-label="다음 최신 글">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                @else
                    <article class="latest-blog-card latest-blog-empty soft-card">
                        <h5 class="latest-blog-title mb-2">아직 등록된 블로그 글이 없습니다.</h5>
                        <p class="latest-blog-desc muted mb-0">공개된 글이 등록되면 이 영역에 최신글 5개가 표시됩니다.</p>
                    </article>
                @endif
            </div>
        </section>

        <section id="highlights" class="section-pad anchor-offset">
            <div class="container reveal">
                <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-4">
                    <div>
                        <span class="kicker mb-2">핵심 섹션</span>
                        <h2 class="fw-bold mb-1">개발자 성장 흐름을 한눈에</h2>
                        <p class="muted mb-0">현재 TH-Study의 핵심 주제를 카드로 정리했습니다.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <article class="p-4 soft-card highlight-card h-100">
                            <i class="devicon-laravel-plain colored highlight-icon" aria-hidden="true"></i>
                            <h5 class="fw-bold mt-3">성장 기록</h5>
                            <p class="muted mb-0">학습과 시행착오, 프로젝트 흐름을 꾸준히 쌓아 성장 자산으로 만듭니다.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="p-4 soft-card highlight-card h-100">
                            <i class="devicon-python-plain colored highlight-icon" aria-hidden="true"></i>
                            <h5 class="fw-bold mt-3">실험 및 학습</h5>
                            <p class="muted mb-0">레거시부터 신기술까지 직접 실험하고, 검증된 결과를 학습 노트로 남깁니다.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="p-4 soft-card highlight-card h-100">
                            <i class="devicon-nginx-original colored highlight-icon" aria-hidden="true"></i>
                            <h5 class="fw-bold mt-3">운영 / 개발일지</h5>
                            <p class="muted mb-0">배포, 로그, 인프라 운영 경험을 개발일지 형태로 구조화해 공유합니다.</p>
                        </article>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <article class="p-4 soft-card highlight-card h-100">
                            <i class="devicon-mysql-plain colored highlight-icon" aria-hidden="true"></i>
                            <h5 class="fw-bold mt-3">수익화 / 프로젝트 진행상황</h5>
                            <p class="muted mb-0">트래픽과 콘텐츠 전략을 기반으로 수익화 가능성과 진행 현황을 관리합니다.</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="section-pad anchor-offset">
            <div class="container reveal">
                <div class="row g-4 g-xl-5 align-items-stretch">
                    <div class="col-lg-5">
                        <div class="project-intro">
                            <span class="kicker mb-3">Real Projects</span>
                            <h2 class="fw-bold mb-3">실제 프로젝트</h2>
                            <p class="mb-3">
                                티에이치스터디는 블로그 글만 쌓는 공간이 아니라, 직접 설계하고 개발하며 운영 경험까지 함께 축적하는 개발자 포트폴리오형 사이트를 지향합니다.
                            </p>
                            <p class="mb-3 muted">
                                기존의 프로젝트 소개가 성장 과정과 운영 철학 중심이었다면, 이제는 “그래서 실제로 무엇을 만들고 있는가”를 메인 화면에서 바로 확인할 수 있도록 구성했습니다.
                            </p>
                            <p class="mb-0 muted">
                                재고관리 PDA처럼 바로 체험 가능한 서비스와, MCP처럼 ChatGPT와 블로그 데이터를 연결하는 AI 실험을 함께 보여주며 기록, 운영, 프로젝트가 하나로 이어지는 흐름을 전달합니다.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div id="projectCarousel" class="carousel slide project-carousel" data-bs-ride="false">
                            <div class="carousel-indicators project-carousel-indicators">
                                @foreach($projects as $index => $project)
                                    <button type="button"
                                            data-bs-target="#projectCarousel"
                                            data-bs-slide-to="{{ $index }}"
                                            class="{{ $index === 0 ? 'active' : '' }}"
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-label="{{ $project['title'] }}"></button>
                                @endforeach
                            </div>

                            <div class="carousel-inner">
                                @foreach($projects as $index => $project)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <article class="project-card soft-card">
                                            <div class="project-card-body">
                                                <div class="project-icon-wrap">
                                                    <i class="bi {{ $project['icon'] }} project-icon" aria-hidden="true"></i>
                                                </div>

                                                <div class="project-copy">
                                                    <h3 class="project-title">{{ $project['title'] }}</h3>
                                                    <p class="project-summary">{{ $project['summary'] }}</p>
                                                    <p class="project-description muted">{{ $project['description'] }}</p>

                                                    <div class="collapse project-detail" id="project-detail-{{ $index }}">
                                                        <div class="project-detail-inner">
                                                            {{ $project['description'] }}
                                                        </div>
                                                    </div>

                                                    <button class="btn project-detail-toggle"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#project-detail-{{ $index }}"
                                                            aria-expanded="false"
                                                            aria-controls="project-detail-{{ $index }}"
                                                            data-more-label="자세히 보기"
                                                            data-less-label="접기">
                                                        자세히 보기
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="project-card-footer">
                                                <div class="project-tags">
                                                    @foreach($project['tags'] as $tag)
                                                        <span class="chip">{{ $tag }}</span>
                                                    @endforeach
                                                </div>

                                                <a class="btn btn-accent project-cta"
                                                   href="{{ $project['href'] }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer">
                                                    {{ $project['buttonText'] }}
                                                </a>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>

                            <div class="project-carousel-controls">
                                <button class="carousel-control-prev project-carousel-control"
                                        type="button"
                                        data-bs-target="#projectCarousel"
                                        data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">이전</span>
                                </button>

                                <button class="carousel-control-next project-carousel-control"
                                        type="button"
                                        data-bs-target="#projectCarousel"
                                        data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">다음</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

          <section class="py-5">
              <div class="container reveal">
                  <div class="soft-card p-4 p-lg-5">
                      <div class="row align-items-center g-3">
                          <div class="col-lg-8">
                              <span class="kicker mb-2">슬로건</span>
                              <h3 class="fw-bold mb-2">“성장하는 개발자에서 장인정신을 지닌 리더로”</h3>
                              <p class="mb-0 muted">
                                  빠르게 끝내는 개발보다, 오래 남는 구조를 만든다. 기록과 운영을 통해 스스로를 업그레이드한다.
                              </p>
                          </div>

                          <div class="col-lg-4">
                              <div class="d-grid gap-2 d-lg-flex justify-content-lg-end">
                                  <a class="btn btn-outline-dark btn-lg" href="#roadmap">로드맵 보기</a>
                                  <a class="btn btn-accent btn-lg" href="#contact">문의하기</a>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <section id="keywords" class="section-pad anchor-offset">
              <div class="container reveal">
                  <span class="kicker mb-2">키워드</span>
                  <h2 class="fw-bold mb-3">스택보다 방향성이 먼저 보이게</h2>
                  <p class="muted mb-4">개발자 성장 플랫폼의 핵심 키워드.</p>

                  <div class="d-flex flex-wrap gap-2">
                      <span class="chip">성장</span>
                      <span class="chip">기록</span>
                      <span class="chip">개발자</span>
                      <span class="chip">실험</span>
                      <span class="chip">웹서비스</span>
                      <span class="chip">지속성</span>
                      <span class="chip">운영</span>
                      <span class="chip">경험</span>
                      <span class="chip">수익화</span>
                      <span class="chip">브랜드</span>
                  </div>
              </div>
          </section>

          <section class="section-pad">
              <div class="container reveal">
                  <div class="row g-4 align-items-center">
                      {{--
                      <div class="col-lg-6">
                        <div class="img-ph sm">No Image</div>
                      </div>
                      --}}
                      <div class="col-lg-6 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('images/extension_logo.png') }}" 
                             class="img-fluid" 
                             style="max-width: 420px; width: 100%; height: auto;">
                      </div>
                      <div class="col-lg-6">
                          <span class="kicker mb-2">Why</span>
                          <div class="why-card p-4">
                              <h2 class="fw-bold mb-3">업무 밖에서 만드는 “내 기준의 개발”</h2>
                              <p class="mb-3">
                                  시스템은 시간이 지나면 낡고, 실무는 늘 일정과 사람 사이에서 타협이 생긴다.
                                  그래서 일상에서 연구하고 정리하는 습관을 서비스로 확장했다.
                              </p>
                              <p class="mb-0 muted">
                                  AI를 도구로 쓰되, 설계와 판단은 사람이 한다는 원칙을 지킨다.
                                  진정성 있는 기록과 꾸준한 개선이 가장 강력한 경쟁력이 된다.
                              </p>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

          <section id="stats" class="section-pad anchor-offset">
              <div class="container reveal">
                  <span class="kicker mb-2">지표</span>
                  <h2 class="fw-bold mb-3">운영을 목표로 하는 프로젝트</h2>
                  <p class="muted mb-4">실제 운영 경험을 쌓기 위해 지표를 만들고 개선한다.</p>

                  <div class="row g-3">
                      <div class="col-md-4">
                          <div class="stat h-100">
                              <div class="num">No Data</div>
                              <div class="label">월 방문자(예정)</div>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="stat h-100">
                              <div class="num">No Data</div>
                              <div class="label">콘텐츠 수(예정)</div>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="stat h-100">
                              <div class="num">No Data</div>
                              <div class="label">운영 로그/지표(예정)</div>
                          </div>
                      </div>
                  </div>

                  <div class="mt-4 soft-card p-4">
                      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                          <div>
                              <div class="fw-bold">“감”이 아니라 “데이터”로 개선한다</div>
                              <div class="muted">유입·행동·성능·운영 로그를 기반으로 개선 포인트를 찾는다.</div>
                          </div>
                          <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#githubShareModal">
                              Repository 보기
                          </button>
                      </div>
                  </div>
              </div>
          </section>

          <section id="profile" class="section-pad anchor-offset">
              <div class="container reveal">
                  <div class="row g-4">
                      <div class="col-lg-6">
                          <span class="kicker mb-2">Profile</span>
                          <h2 class="fw-bold mb-3">개발자 약력</h2>

                          <div class="soft-card p-4">
                              <ul class="mb-0 muted">
                                  <li>MES 및 ERP 시스템 개발 경험</li>
                                  <li>에너지관리시스템 xEMS 개발(BEMS/FEMS/REMS/HEMS)</li>
                                  <li>웹서비스 기획·설계·디자인·개발 전반 수행</li>
                                  <li>PHP 레거시부터 CodeIgniter, Laravel까지 경험</li>
                                  <li>Python, Java, Spring Boot 등 다양한 백엔드 기술 학습·활용</li>
                                  <li>AWS 및 네이버 클라우드 기반 서버 운영 경험</li>
                                  <li>리눅스 서버 직접 설치/구성(Nginx, PHP-FPM, MySQL 등)</li>
                                  <li>로컬 서버 및 개발 환경 구축 경험</li>
                                  <li>로그/보안/배포/트래픽 등 운영 전반 경험</li>
                              </ul>
                          </div>
                      </div>

                      <div class="col-lg-6">
                          <span class="kicker mb-2">Stack</span>
                          <h2 class="fw-bold mb-3">대표 기술</h2>

                          <div class="row g-3">
                              <div class="col-6">
                                  <div class="soft-card p-3 h-100">
                                      <div class="fw-bold">Backend</div>
                                      <div class="muted">PHP, Laravel</div>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="soft-card p-3 h-100">
                                      <div class="fw-bold">Database</div>
                                      <div class="muted">MySQL</div>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="soft-card p-3 h-100">
                                      <div class="fw-bold">Frontend</div>
                                      <div class="muted">Bootstrap 5, jQuery</div>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="soft-card p-3 h-100">
                                      <div class="fw-bold">Infra</div>
                                      <div class="muted">Ubuntu, Nginx, Cloud(AWS/NCP)</div>
                                  </div>
                              </div>
                          </div>

                          <div class="mt-4 soft-card p-4">
                              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                  <div>
                                      <div class="fw-bold"><i class="bi bi-github me-2"></i>GitHub</div>
                                      <div class="muted">소스는 GitHub에서 확인 가능.</div>
                                  </div>
                                  <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#githubShareModal">
                                      Repository 열기
                                  </button>
                              </div>
                          </div>

                          {{--
                          <div class="mt-3 img-ph sm">No Image</div>
                          --}}

                          <div class="video-card mt-3">
                              <div class="video-head d-flex align-items-center justify-content-between mb-2">
                                  <div class="fw-bold">Laravel 학습 영상</div>
                                  <a class="small muted text-decoration-none" target="_blank" rel="noopener noreferrer"
                                     href="https://youtu.be/vYrTMfEufsg?si=kQek9rnZM2i7DKGc">
                                      YouTube 열기
                                  </a>
                              </div>
                              <div class="video-ratio yt-lite"
                                   role="button"
                                   tabindex="0"
                                   aria-label="Laravel 유튜브 영상 재생"
                                   data-video-id="vYrTMfEufsg"
                                   data-youtube-url="https://youtu.be/vYrTMfEufsg?si=kQek9rnZM2i7DKGc">
                                  <img
                                      src="https://i.ytimg.com/vi/vYrTMfEufsg/hqdefault.jpg"
                                      alt="Laravel 유튜브 영상 썸네일"
                                      loading="lazy">
                                  <span class="yt-play-btn" aria-hidden="true"></span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </section>

        <section id="roadmap" class="section-pad anchor-offset">
            <div class="container reveal">
                  <span class="kicker mb-2">Roadmap</span>
                  <h2 class="fw-bold mb-3">지금-다음-그다음</h2>
                  <p class="muted mb-4">확장 가능한 방식으로, 천천히 단단하게.</p>

                  <div class="row g-3">
                      <div class="col-lg-4">
                          <div class="soft-card p-4 h-100">
                              <div class="fw-bold mb-2">Now</div>
                              <ul class="mb-0 muted">
                                  <li>메인 소개/콘텐츠 구조 정리</li>
                                  <li>기록/게시/기본 운영 기능 안정화</li>
                                  <li>로그 기반 운영 품질 개선</li>
                              </ul>
                          </div>
                      </div>
                      <div class="col-lg-4">
                          <div class="soft-card p-4 h-100">
                              <div class="fw-bold mb-2">Next</div>
                              <ul class="mb-0 muted">
                                  <li>유입/행동 지표 대시보드</li>
                                  <li>큐/메일/알림 등 운영 자동화</li>
                                  <li>콘텐츠 카테고리화(여행/개발/문서)</li>
                              </ul>
                          </div>
                      </div>
                      <div class="col-lg-4">
                          <div class="soft-card p-4 h-100">
                              <div class="fw-bold mb-2">Later</div>
                              <ul class="mb-0 muted">
                                  <li>PWA 설치형 웹앱 실험</li>
                                  <li>수익화(광고/콘텐츠) 최적화</li>
                                  <li>운영 경험을 제품화(서비스화)</li>
                              </ul>
                          </div>
                      </div>
                  </div>

                  <div class="mt-4 soft-card p-4">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                          <div>
                              <div class="fw-bold">함께 이야기해보기</div>
                              <div class="muted">콜라보/피드백/제안이 있으면 편하게.</div>
                          </div>
                          <a class="btn btn-accent" href="#contact">문의하기</a>
                      </div>
                  </div>
            </div>
        </section>

        <section id="contact" class="section-pad anchor-offset">
            <div class="container reveal">
                <span class="kicker mb-2">Contact</span>
                <h2 class="fw-bold mb-3">문의하기</h2>
                <p class="muted mb-4">문의 내용을 남겨주시면 확인 후 빠르게 연락드리겠습니다.</p>

                <div class="soft-card contact-shell p-4 p-lg-5">
                    <div id="contactErrors" class="alert alert-danger d-none" role="alert"></div>

                    <form id="contact_form" method="post" action="{{ route('guest_posts.store', ['post_type' => $postType]) }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">이름</label>
                                <input class="form-control" id="name" name="name" placeholder="홍길동" />
                                <div id="error-name" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">연락 방법</label>
                                <div class="d-flex flex-wrap gap-3 contact-method-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_method" id="contact_phone" value="phone" checked>
                                        <label class="form-check-label" for="contact_phone">핸드폰</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="contact_method" id="contact_mail" value="email">
                                        <label class="form-check-label" for="contact_mail">이메일</label>
                                    </div>
                                </div>
                                <div id="error-contact_method" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>

                            <div class="col-md-6" id="contactPhoneGroup">
                                <label class="form-label" for="phone">핸드폰 번호</label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       class="form-control"
                                       placeholder="하이픈 없이 숫자만 입력해 주세요. 예) 01012345678" />
                                <div id="error-phone" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>

                            <div class="col-md-6 d-none" id="contactEmailGroup">
                                <label class="form-label" for="email">이메일</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control"
                                       placeholder="email@example.com" />
                                <div id="error-email" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="inquiry_memo">문의 내용</label>
                                <textarea id="inquiry_memo" name="inquiry_memo" class="form-control" rows="5" placeholder="내용을 입력해 주세요."></textarea>
                                <div id="error-inquiry_memo" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox"
                                           id="personal_info_agree"
                                           name="personal_info_agree"
                                           class="form-check-input"
                                           value="Y"
                                           checked>
                                    <label class="form-check-label small muted" for="personal_info_agree">
                                        개인정보 수집 및 이용에 동의합니다.
                                    </label>
                                </div>
                                <div id="error-personal_info_agree" class="invalid-feedback d-block small text-break d-none"></div>

                                <div class="form-check mt-2">
                                    <input type="checkbox"
                                           id="marketing_info_agree"
                                           name="marketing_info_agree"
                                           class="form-check-input"
                                           value="Y">
                                    <label class="form-check-label small muted" for="marketing_info_agree">
                                        마케팅 정보 수신에 동의합니다.
                                    </label>
                                </div>
                                <div id="error-marketing_info_agree" class="invalid-feedback d-block small text-break d-none"></div>
                            </div>
                        </div>
                    </form>

                    <div class="contact-actions mt-4">
                        <button type="button" id="contactSubmitBtn" class="btn btn-accent btn-lg">문의하기</button>
                    </div>
                </div>
            </div>
        </section>

        <section id="map" class="section-pad anchor-offset">
            <div class="container reveal">
                <span class="kicker mb-2">Location</span>
                <h2 class="fw-bold mb-2">위치 안내</h2>
                <p class="muted mb-4">경기도 안산시 단원구 시화호수로633 (반달섬)</p>
                <div class="map-shell soft-card">
                    <div id="kakao-map" class="map-canvas" aria-label="Kakao Map"></div>
                    <div class="map-placeholder">Map Area</div>
                </div>
            </div>

            <div class="text-center my-3 d-block d-md-none">
                <x-adfit
                    :unit="config('adfit.mobile.rectangle.unit')"
                    :width="config('adfit.mobile.rectangle.width')"
                    :height="config('adfit.mobile.rectangle.height')" />
            </div>

            <div class="text-center my-3 d-none d-md-block">
                <x-adfit
                    :unit="config('adfit.pc.rectangle.unit')"
                    :width="config('adfit.pc.rectangle.width')"
                    :height="config('adfit.pc.rectangle.height')" />
            </div>

        </section>

          <div class="modal fade" id="contactConfirmModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                      <div class="modal-header border-0">
                          <h5 class="modal-title fw-bold">문의 확인</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
                      </div>
                      <div class="modal-body">
                          작성하신 내용으로 문의를 진행하시겠습니까?
                      </div>
                      <div class="modal-footer border-0">
                          <button type="button" id="btn_cancel" class="btn btn-outline-secondary" data-bs-dismiss="modal">아니오</button>
                          <button type="button" id="btn_save_inquire" class="btn btn-accent">예</button>
                      </div>
                  </div>
              </div>
          </div>

          <div class="modal fade" id="githubShareModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                      <div class="modal-header border-0">
                          <h5 class="modal-title fw-bold">깃허브 소스 공유 안내</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
                      </div>
                      <div class="modal-body">
                          <p class="muted mb-3">깃허브 소스 공유 및 협업은 아래 요청 바랍니다.</p>
                          <ol class="mb-0 muted">
                              <li>사이트 소개 &gt; 문의하기</li>
                              <li>회원 가입 후 나의 문의내역에 요청해주세요.</li>
                          </ol>
                      </div>
                      <div class="modal-footer border-0">
                          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">닫기</button>
                          <a class="btn btn-accent" href="#contact" id="githubToContactBtn">문의하기 이동</a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </main>
@endsection

@section('script')
    <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey={{ config('services.kakao.app_key') }}"></script>
    <script>
        $(function(){

            $('input[name="contact_method"]').on('change', updateContactMethodVisibility);

            $('#contact_form').on('submit', function(e) {
                e.preventDefault();
            });

            $('#contact_form').on('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });

            $('#contactSubmitBtn').on('click',function() {
                $('#contactConfirmModal').modal('show');
            });

            $('#btn_save_inquire').on('click', function() {
                const isValid = validateContactForm();
                if (!isValid) {
                    $('#contactConfirmModal').modal('hide');
                    return;
                }
                $('#contactConfirmModal').modal('hide');
                submitContactForm();
            });

            $('#contactConfirmModal').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
            $('#contactConfirmModal').on('show.bs.modal', function() {
                $(document).on('keydown.contactConfirm', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            });
            $('#contactConfirmModal').on('hidden.bs.modal', function() {
                $(document).off('keydown.contactConfirm');
            });

            $('#githubToContactBtn').on('click', function(e) {
                e.preventDefault();
                const target = document.getElementById('contact');
                $('#githubShareModal').modal('hide');

                if (!target) return;
                setTimeout(function() {
                    const y = target.getBoundingClientRect().top + window.scrollY - 88;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }, 220);
            });

            if (typeof initLiteYouTubeEmbeds === 'function') {
                initLiteYouTubeEmbeds(document);
            }

            initKakaoMap();
            initHomeScroll();
            initHeroTyping();
            updateContactMethodVisibility();
            bindProjectDetailToggles();
        });


        function bindProjectDetailToggles() {
            document.querySelectorAll('.project-detail-toggle').forEach(function(button) {
                const targetSelector = button.getAttribute('data-bs-target');
                const target = targetSelector ? document.querySelector(targetSelector) : null;
                const moreLabel = button.getAttribute('data-more-label') || '자세히 보기';
                const lessLabel = button.getAttribute('data-less-label') || '접기';

                if (!target) return;

                const updateLabel = function() {
                    button.textContent = target.classList.contains('show') ? lessLabel : moreLabel;
                };

                target.addEventListener('shown.bs.collapse', updateLabel);
                target.addEventListener('hidden.bs.collapse', updateLabel);
                updateLabel();
            });
        }

        function initKakaoMap(){
            const mapContainer = document.getElementById('kakao-map');
            if (!mapContainer || !window.kakao || !window.kakao.maps) return;

            const lat = parseFloat("{{ config('services.kakao.map_lat') }}");
            const lng = parseFloat("{{ config('services.kakao.map_lng') }}");

            const options = {
                center: new kakao.maps.LatLng(lat, lng),
                level: 3,
            };

            const map = new kakao.maps.Map(mapContainer, options);
            const markerPosition = new kakao.maps.LatLng(lat, lng);
            const marker = new kakao.maps.Marker({ position: markerPosition });
            marker.setMap(map);

            const placeholder = mapContainer.parentElement.querySelector('.map-placeholder');
            if (placeholder) placeholder.style.display = 'none';
        }

        function submitContactForm() {
            const $form = $('#contact_form');
            const $errors = $('#contactErrors');
            const $errorFields = $('#error-name, #error-contact_method, #error-phone, #error-email, #error-inquiry_memo, #error-personal_info_agree, #error-marketing_info_agree');
            const $invalidInputs = $('#name, #phone, #email, #inquiry_memo');
            const payload = {
                name: $('#name').val(),
                contact_method: $('input[name="contact_method"]:checked').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                inquiry_memo: $('#inquiry_memo').val(),
                personal_info_agree: $('#personal_info_agree').is(':checked') ? $('#personal_info_agree').val() : 'N',
                marketing_info_agree: $('#marketing_info_agree').is(':checked') ? $('#marketing_info_agree').val() : 'N',
            };
            const checkboxState = {
                personalInfo: $('#personal_info_agree').is(':checked'),
                marketingInfo: $('#marketing_info_agree').is(':checked'),
            };

            $errors.addClass('d-none').text('');
            $errorFields.addClass('d-none').text('');
            $invalidInputs.removeClass('is-invalid');
            $('input[name="contact_method"]').removeClass('is-invalid');
            $('#personal_info_agree').removeClass('is-invalid');
            $('#marketing_info_agree').removeClass('is-invalid');

            requestAjax({
                method: 'POST',
                url: $form.attr('action'),
                dataType: 'json',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                onSuccess: function () {
                    $('#contactConfirmModal').modal('hide');
                    resetContactForm();
                },
                onError: function (xhr) {
                    $('#personal_info_agree').prop('checked', checkboxState.personalInfo);
                    $('#marketing_info_agree').prop('checked', checkboxState.marketingInfo);

                    let res = xhr.responseJSON;
                    if (!res && xhr.responseText) {
                        try {
                            res = JSON.parse(xhr.responseText);
                        } catch (e) {
                            res = null;
                        }
                    }
                    res = res || {};
                    const errors = res.errors || {};
                    const statusMessages = errors.status || [];
                    const fieldMap = {
                        name: '#name',
                        contact_method: 'input[name="contact_method"]',
                        phone: '#phone',
                        email: '#email',
                        inquiry_memo: '#inquiry_memo',
                        personal_info_agree: '#personal_info_agree',
                        marketing_info_agree: '#marketing_info_agree',
                    };
                    const errorMap = {
                        name: '#error-name',
                        contact_method: '#error-contact_method',
                        phone: '#error-phone',
                        email: '#error-email',
                        inquiry_memo: '#error-inquiry_memo',
                        personal_info_agree: '#error-personal_info_agree',
                        marketing_info_agree: '#error-marketing_info_agree',
                    };
                    let hasFieldErrors = false;

                    $.each(errors, function(field, fieldMessages){
                        const baseField = field.split('.')[0];
                        const selector = fieldMap[baseField];
                        const errorSelector = errorMap[baseField];
                        if (!selector || !errorSelector) {
                            return;
                        }
                        hasFieldErrors = true;
                        $(selector).addClass('is-invalid');
                        $(errorSelector).removeClass('d-none').text(fieldMessages.join(' '));
                    });

                    if (statusMessages.length) {
                        $errors.removeClass('d-none').text(statusMessages.join(' '));
                    }

                    if (!hasFieldErrors && !statusMessages.length) {
                        $errors.removeClass('d-none').text('문의사항 등록 실패 사유를 확인해주세요.');
                    }
                },
            });
        }

        function validateContactForm() {
            const $errors = $('#contactErrors');
            const $name = $('#name');
            const $phone = $('#phone');
            const $memo = $('#inquiry_memo');
            const $personalInfo = $('#personal_info_agree');
            const method = $('input[name="contact_method"]:checked').val();

            const $errorName = $('#error-name');
            const $errorPhone = $('#error-phone');
            const $errorMemo = $('#error-inquiry_memo');
            const $errorPersonal = $('#error-personal_info_agree');

            $errors.addClass('d-none').text('');
            $errorName.addClass('d-none').text('');
            $errorPhone.addClass('d-none').text('');
            $errorMemo.addClass('d-none').text('');
            $errorPersonal.addClass('d-none').text('');

            $name.removeClass('is-invalid');
            $phone.removeClass('is-invalid');
            $memo.removeClass('is-invalid');
            $personalInfo.removeClass('is-invalid');

            let isValid = true;
            const name = $.trim($name.val());
            const phone = $.trim($phone.val());
            const memo = $.trim($memo.val());
            const personalChecked = $personalInfo.is(':checked');

            if (!name) {
                $errorName.removeClass('d-none').text('이름을 입력해주세요.');
                $name.addClass('is-invalid');
                isValid = false;
            }

            if (method === 'phone' && !phone) {
                $errorPhone.removeClass('d-none').text('핸드폰 번호를 입력해주세요.');
                $phone.addClass('is-invalid');
                isValid = false;
            }

            if (method === 'email' && !$.trim($('#email').val())) {
                $('#error-email').removeClass('d-none').text('이메일을 입력해주세요.');
                $('#email').addClass('is-invalid');
                isValid = false;
            }

            if (!memo) {
                $errorMemo.removeClass('d-none').text('문의 내용을 입력해주세요.');
                $memo.addClass('is-invalid');
                isValid = false;
            }

            if (!personalChecked) {
                $errorPersonal.removeClass('d-none').text('개인정보 수집 및 이용에 동의해주세요.');
                $personalInfo.addClass('is-invalid');
                isValid = false;
            }

            return isValid;
        }

        function resetContactForm() {
            const $form = $('#contact_form');
            if ($form.length && $form[0]) {
                $form[0].reset();
            }

            const $errors = $('#contactErrors');
            const $errorFields = $('#error-name, #error-contact_method, #error-phone, #error-email, #error-inquiry_memo, #error-personal_info_agree, #error-marketing_info_agree');
            const $invalidInputs = $('#name, #phone, #email, #inquiry_memo');

            $errors.addClass('d-none').text('');
            $errorFields.addClass('d-none').text('');
            $invalidInputs.removeClass('is-invalid');
            $('input[name="contact_method"]').removeClass('is-invalid');
            $('#personal_info_agree').removeClass('is-invalid');
            $('#marketing_info_agree').removeClass('is-invalid');

            updateContactMethodVisibility();
        }

        function updateContactMethodVisibility(){
            const $phoneGroup = $('#contactPhoneGroup');
            const $emailGroup = $('#contactEmailGroup');
            const method = $('input[name="contact_method"]:checked').val();
            
            if (method === 'email') {
                $('#phone').val('');
                $emailGroup.removeClass('d-none');
                $phoneGroup.addClass('d-none');
            } else {
                $('#email').val('');
                $phoneGroup.removeClass('d-none');
                $emailGroup.addClass('d-none');
            }
        }
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('js/intro/home.js') }}?v={{ filemtime(public_path('js/intro/home.js')) }}"></script>
@endpush
