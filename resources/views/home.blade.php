@extends('layouts.app')

@section('title', '홈')

@section('style')
<style>
    body{
        background: #0b1220;
    }

    .home-landing{
        --bg: #0c1220;
        --bg2: #0f172a;
        --text: rgba(248,250,252,.96);
        --muted: rgba(226,232,240,.78);
        --accent: #6aa6ff;
        --accent2: #38d2ff;
        --cardRadius: 18px;
        --shadow: 0 18px 38px rgba(2, 6, 23, .45);
    }

    .home-landing{
        background: radial-gradient(900px 420px at 10% 0%, rgba(106,166,255,.16), transparent 50%),
                    radial-gradient(700px 420px at 90% 5%, rgba(56,210,255,.16), transparent 48%),
                    linear-gradient(180deg, var(--bg), var(--bg2));
        color: var(--text);
        border: 1px solid rgba(148,163,184,.22);
        border-radius: 18px;
        padding: 8px 0;
        margin-left: -1rem;
        margin-right: -1rem;
    }
    @media (min-width: 992px){
        .home-landing{
            margin-left: -1.5rem;
            margin-right: -1.5rem;
        }
    }

      .home-landing a{ color: inherit; }
      .home-landing .muted{ color: var(--muted); }
      .home-landing .section-pad{ padding: 72px 0; }
      .home-landing .anchor-offset{ scroll-margin-top: 96px; }
      .home-landing .fw-black{ font-weight: 800; }
      .home-landing h1,
      .home-landing h2,
      .home-landing h3{
          font-weight: 800;
          letter-spacing: -.02em;
      }

    .home-landing .btn-accent{
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border: 0;
        color: #0b1220;
        font-weight: 700;
    }
    .home-landing .btn-accent:hover{ opacity: .95; color: #0b1220; }

    .home-landing .btn-outline-dark,
    .home-landing .btn-outline-secondary{
        border-color: rgba(148,163,184,.5);
        color: rgba(248,250,252,.95);
        background: rgba(15, 23, 42, .35);
    }
    .home-landing .btn-outline-dark:hover,
    .home-landing .btn-outline-secondary:hover{
        border-color: rgba(148,163,184,.8);
        color: rgba(248,250,252,1);
        background: rgba(30, 41, 59, .6);
    }

    .home-landing .soft-card{
        background: rgba(15, 23, 42, .65);
        border: 1px solid rgba(148,163,184,.22);
        border-radius: var(--cardRadius);
        box-shadow: var(--shadow);
    }
    .home-landing .soft-card.flat{
        box-shadow: none;
        background: rgba(15, 23, 42, .45);
    }

    .home-landing .chip{
        border: 1px solid rgba(148,163,184,.28);
        background: rgba(15, 23, 42, .55);
        color: rgba(248,250,252,.9);
        padding: .5rem .9rem;
        border-radius: 999px;
        font-weight: 650;
        font-size: .92rem;
    }

    .home-landing .img-ph{
        background:
            linear-gradient(135deg, rgba(106,166,255,.18), rgba(56,210,255,.12)),
            rgba(15, 23, 42, .4);
        border: 1px dashed rgba(148,163,184,.32);
        border-radius: var(--cardRadius);
        min-height: 230px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(226,232,240,.8);
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        user-select: none;
      }
      .home-landing .img-ph.sm{ min-height: 170px; }
      .home-landing .img-ph.lg{ min-height: 380px; }

    .home-landing .kicker{
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .4rem .75rem;
        border-radius: 999px;
        background: rgba(106,166,255,.16);
        border: 1px solid rgba(106,166,255,.28);
        color: rgba(226,232,240,.95);
        font-weight: 700;
        font-size: .9rem;
    }
    .home-landing .grad-title{
        background: linear-gradient(135deg, #f8fafc, #7dd3fc);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .home-landing .stat{
        padding: 22px;
        border-radius: var(--cardRadius);
        background: rgba(15, 23, 42, .65);
        border: 1px solid rgba(148,163,184,.22);
    }
      .home-landing .stat .num{
          font-size: 2rem;
          font-weight: 800;
          letter-spacing: -.02em;
          line-height: 1.1;
      }
      .home-landing .stat .label{
          color: var(--muted);
          margin-top: .25rem;
          font-weight: 650;
      }

    .home-landing .modal-content{
        background: rgba(12, 18, 32, .96);
        border: 1px solid rgba(148,163,184,.22);
        border-radius: var(--cardRadius);
        color: var(--text);
    }
    .home-landing .modal-header .btn-close{
        filter: brightness(0) invert(1);
        opacity: .95;
    }
    .home-landing .modal-header .btn-close:focus{
        box-shadow: none;
    }
    #loadingModal{
        z-index: 2000;
    }
    #loadingModal .modal-dialog{
        z-index: 2001;
    }
    #loadingModal .modal-content{
        background: rgba(10, 16, 30, 0.98);
        border: 1px solid rgba(148, 163, 184, 0.4);
        box-shadow: 0 28px 60px rgba(2, 6, 23, 0.7);
    }
    .modal-backdrop.loading-backdrop{
        background: #000 !important;
        opacity: 0.78 !important;
    }
    .home-landing .form-control,
    .home-landing .form-select,
    .home-landing textarea{
        background: rgba(15, 23, 42, .72) !important;
        border: 1px solid rgba(148,163,184,.28) !important;
        color: rgba(248,250,252,.95) !important;
        resize: none;
    }
    .home-landing .form-control::placeholder,
    .home-landing textarea::placeholder{ color: rgba(226,232,240,.6); }

    .home-landing #backToTop{
        width: 52px;
        height: 52px;
        border-radius: 999px;
        display: none;
        z-index: 1060;
        box-shadow: 0 10px 26px rgba(2, 6, 23, .45);
        border: 1px solid rgba(148,163,184,.22);
        background: rgba(15, 23, 42, .88);
        color: rgba(248,250,252,.95);
        touch-action: manipulation;
    }
    .home-landing #backToTop:hover{ background: rgba(15, 23, 42, .96); }

    .home-landing .profile-image{
        display: flex;
        justify-content: center;
        margin-top: 16px;
    }
    .home-landing .image-card{
        display: block;
        width: 100%;
        padding: 18px;
        border-radius: var(--cardRadius);
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(15, 23, 42, .35);
        box-shadow: 0 18px 40px rgba(2, 6, 23, .35);
    }
    .home-landing .profile-image img{
        max-width: 320px;
        max-height: 180px;
        width: 100%;
        height: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .home-landing .why-card{
        background: rgba(15, 23, 42, .55);
        border: 1px solid rgba(148,163,184,.22);
        border-radius: var(--cardRadius);
    }

    .home-landing .map-shell{
        min-height: 520px;
        border-radius: calc(var(--cardRadius) + 8px);
        border: 1px solid rgba(148,163,184,.22);
        background:
            linear-gradient(135deg, rgba(106,166,255,.16), rgba(56,210,255,.08)),
            rgba(15, 23, 42, .55);
        position: relative;
        overflow: hidden;
    }
    .home-landing .map-canvas{
        position: absolute;
        inset: 0;
    }
    .home-landing .map-placeholder{
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        color: rgba(226,232,240,.85);
        text-transform: uppercase;
        letter-spacing: .18em;
        font-weight: 800;
        font-size: .95rem;
    }

    .home-landing .reveal{
        opacity: 0;
        transform: translateY(14px);
        transition: opacity .6s ease, transform .6s ease;
        will-change: opacity, transform;
    }
    .home-landing .reveal.is-visible{
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 767.98px){
        .home-landing{
            padding: 0;
        }
        .home-landing .section-pad{
            padding: 56px 0;
        }
        .home-landing .py-5{
            padding: 32px 0 !important;
        }
        .home-landing .image-card{
            padding: 14px;
        }
        .home-landing .map-shell{
            min-height: 360px;
        }
    }
</style>
@endsection

@section('content')
<main class="col-12">
    <div class="home-landing">
        <div id="topSentinel" style="position:absolute; top:0; left:0; width:1px; height:1px;"></div>

        <header id="top" class="section-pad">
            <div class="container reveal">
                <div class="row align-items-center g-4">
                      <div class="col-lg-6">
                          <span class="kicker mb-3">Developer Growth Platform</span>
                          <h1 class="display-5 fw-black mb-3 grad-title">티에이치스터디그룹</h1>
                          <p class="lead mb-4 muted">
                              성장하는 개발자가 장인정신을 지닌 리더로 나아가는 과정을 기록하고 확장하는 개발자 성장 플랫폼.
                          </p>

                          <div class="d-flex flex-wrap gap-2 mb-4">
                              <span class="chip">성장</span>
                              <span class="chip">기록</span>
                              <span class="chip">실험</span>
                              <span class="chip">운영</span>
                              <span class="chip">수익화</span>
                          </div>

                          <div class="d-flex gap-2 flex-wrap">
                              <a class="btn btn-accent btn-lg" href="#highlights">핵심 요약 보기</a>
                              <a class="btn btn-outline-dark btn-lg" href="#about">프로젝트 소개</a>
                          </div>
                      </div>

                      {{--
                      <div class="col-lg-6">
                          <div class="img-ph lg">No Image</div>
                      </div>
                      --}}

                      <div class="col-lg-6 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('images/main_logo.png') }}" 
                             class="img-fluid" 
                             style="max-width: 420px; width: 100%; height: auto;">
                      </div>
                  </div>
              </div>
          </header>

          <section id="about" class="section-pad anchor-offset">
              <div class="container reveal">
                  <div class="row g-4 align-items-center">
                      <div class="col-lg-6">
                          <span class="kicker mb-3">프로젝트 소개</span>
                          <h2 class="fw-bold mb-3">기록이 쌓이고, 운영이 경험이 되고, 경험이 자산이 된다</h2>
                          <p class="mb-3">
                              티에이치스터디그룹은 개발자로 살아온 시간과 앞으로의 성장을 담아내기 위해 시작된 개발자 성장 플랫폼이다.
                              학습, 경험, 기록, 실험, 서비스 운영을 하나의 흐름으로 연결한다.
                          </p>
                          <p class="mb-0 muted">
                              PHP 레거시부터 프레임워크까지의 경험을 기반으로, 기존 기술과 새로운 기술이 공존하는 환경을 직접 만들고 운영한다.
                              작은 기록 습관에서 출발해, 하나의 서비스로 확장하며 “꾸준히 오래 가는 개발”을 목표로 한다.
                          </p>
                      </div>

                      {{--
                      <div class="col-lg-6">
                          <div class="img-ph soft-card flat">No Image</div>
                      </div>
                      --}}

                      <div class="col-lg-6 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('images/intro_project_img.jpg') }}" 
                             class="img-fluid" 
                             style="max-width: 420px; width: 100%; height: auto;">
                      </div>
                  </div>
              </div>
          </section>

          <section id="highlights" class="section-pad anchor-offset">
              <div class="container reveal">
                  <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-4">
                      <div>
                          <span class="kicker mb-2">핵심 포인트</span>
                          <h2 class="fw-bold mb-1">한 페이지에서 성격이 바로 읽히게</h2>
                          <p class="muted mb-0">개발자 성장 플랫폼으로서의 본질만 남겼다.</p>
                      </div>
                  </div>

                  <div class="row g-3">
                      <div class="col-md-6 col-lg-3">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">성장 기록</h5>
                              <p class="muted mb-0">학습, 시행착오, 프로젝트 흐름을 기록하고 정리한다.</p>
                          </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">레거시 × 신기술</h5>
                              <p class="muted mb-0">오래된 기술의 현실과 최신 흐름을 함께 실험한다.</p>
                          </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">서비스 확장</h5>
                              <p class="muted mb-0">개발을 넘어 기획, 운영, 개선까지 연결한다.</p>
                          </div>
                      </div>
                      <div class="col-md-6 col-lg-3">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">운영 경험</h5>
                              <p class="muted mb-0">로그, 배포, 트래픽 등 실제 운영 감각을 쌓는다.</p>
                          </div>
                      </div>
                  </div>

                  <div class="row g-3 mt-1">
                      <div class="col-md-6 col-lg-6">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">정보 정리 플랫폼</h5>
                              <p class="muted mb-0">개발 지식, 문서, 여행 기록까지 한곳에 모아 관리한다.</p>
                          </div>
                      </div>
                      <div class="col-md-6 col-lg-6">
                          <div class="p-4 soft-card h-100">
                              <h5 class="fw-bold">성장과 수익의 연결</h5>
                              <p class="muted mb-0">광고/콘텐츠/트래픽을 통해 지속 가능한 프로젝트를 만든다.</p>
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
                                  <button class="btn btn-accent btn-lg" data-bs-toggle="modal" data-bs-target="#contactModal">
                                      문의하기
                                  </button>
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
                          <a class="btn btn-outline-dark" href="https://github.com/leetaehee/laravel-app" target="_blank" rel="noopener">Repository 보기</a>
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
                                  <a class="btn btn-accent" href="https://github.com/leetaehee/laravel-app" target="_blank" rel="noopener">Repository 열기</a>
                              </div>
                          </div>

                          {{--
                          <div class="mt-3 img-ph sm">No Image</div>
                          --}}

                          <div class="profile-image">
                              <div class="image-card">
                                  <img src="{{ asset('images/intro_developer_history.jpg') }}" 
                                       class="img-fluid">
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
                          <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#contactModal">문의하기</button>
                      </div>
                  </div>
            </div>
        </section>

        <section id="map" class="section-pad anchor-offset">
            <div class="container reveal">
                <span class="kicker mb-2">Location</span>
                <h2 class="fw-bold mb-3">찾아오는 길</h2>
                <p class="muted mb-4">경기도 안산시 단원구 시화호수로633 (반달섬)</p>
                <div class="map-shell soft-card">
                    <div id="kakao-map" class="map-canvas" aria-label="Kakao Map"></div>
                    <div class="map-placeholder">Map Area</div>
                </div>
            </div>
        </section>

        <button id="backToTop"
                class="position-fixed d-flex align-items-center justify-content-center"
                style="bottom: calc(18px + env(safe-area-inset-bottom)); bottom: calc(18px + constant(safe-area-inset-bottom)); right: 16px;"
                aria-label="맨 위로">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 14l6-6 6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                      <div class="modal-header border-0">
                          <h5 class="modal-title fw-bold">문의하기</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
                      </div>

                      <div class="modal-body">
                          <p class="muted small mb-3">
                              문의 내용을 남겨주시면 확인 후 빠르게 연락드리겠습니다.
                          </p>

                          <div id="contactErrors" class="alert alert-danger d-none" role="alert"></div>

                          <form id="contact_form" method="post" action="/contact">
                              <div class="mb-3">
                                  <label class="form-label" for="name">이름</label>
                                  <input class="form-control" id="name" name="name" placeholder="홍길동" />
                                  <div id="error-name" class="invalid-feedback d-block small text-break d-none"></div>
                              </div>

                              <div class="mb-3">
                                  <label class="form-label">연락 방법</label>
                                  <div class="d-flex flex-wrap gap-3">
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

                              <div class="mb-3" id="contactPhoneGroup">
                                  <label class="form-label" for="phone">핸드폰 번호</label>
                                  <input type="tel"
                                         id="phone" 
                                         name="phone"
                                         class="form-control"
                                         placeholder="010-0000-0000" />
                                  <div id="error-phone" class="invalid-feedback d-block small text-break d-none"></div>
                              </div>

                              <div class="mb-3 d-none" id="contactEmailGroup">
                                  <label class="form-label" for="email">이메일</label>
                                  <input type="email" 
                                         id="email" 
                                         name="email" 
                                         class="form-control"
                                         placeholder="email@example.com" />
                                  <div id="error-email" class="invalid-feedback d-block small text-break d-none"></div>
                              </div>

                              <div class="mb-3">
                                  <label class="form-label" for="inquire_memo">문의 내용</label>
                                  <textarea id="inquire_memo" name="inquire_memo" class="form-control" rows="4" placeholder="내용을 입력해 주세요."></textarea>
                                  <div id="error-inquire_memo" class="invalid-feedback d-block small text-break d-none"></div>
                              </div>

                              <div class="form-check">
                                  <input type="checkbox" value="1" id="personal_info_agree" name="personal_info_agree" class="form-check-input" >
                                  <label class="form-check-label small muted" for="personal_info_agree">
                                      개인정보 수집 및 이용에 동의합니다.
                                  </label>
                              </div>
                              <div id="error-personal_info_agree" class="invalid-feedback d-block small text-break d-none"></div>
                          </form>
                      </div>

                      <div class="modal-footer border-0">
                          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">닫기</button>
                          <button type="button" id="contactSubmitBtn" class="btn btn-accent">문의하기</button>
                      </div>
                  </div>
              </div>
          </div>

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

            $('#contactSubmitBtn').on('click',function() {
                $('#contactConfirmModal').modal('show');
            });

            $('#btn_save_inquire').on('click', function() {
                $('#contactConfirmModal').modal('hide');
                submitContactForm();
            });

            initKakaoMap();
            initHomeScroll();
            updateContactMethodVisibility();
        });

        function initHomeScroll(){
            const backToTopBtn = document.getElementById('backToTop');
            const sentinel = document.getElementById('topSentinel');

            function smoothScrollTo(targetY, durationMs) {
                const startY = window.scrollY;
                const diff = targetY - startY;
                const start = performance.now();

                function ease(t){
                    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
                }

                function step(now){
                    const p = Math.min(1, (now - start) / durationMs);
                    window.scrollTo(0, startY + diff * ease(p));
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            document.querySelectorAll('.home-landing a[href^="#"]').forEach((anchor) => {
                anchor.addEventListener('click', (event) => {
                    const id = anchor.getAttribute('href');
                    if (!id || id.length < 2) return;
                    const el = document.querySelector(id);
                    if (!el) return;
                    event.preventDefault();
                    const y = el.getBoundingClientRect().top + window.scrollY - 96;
                    smoothScrollTo(y, 720);
                });
            });
    
            /*function enableWheelSmooth(){
                let current = window.scrollY;
                let target = window.scrollY;
                let ticking = false;

                const strength = 0.1;
                const maxStep = 220;

                function animate(){
                    ticking = true;
                    current += (target - current) * strength;
                    window.scrollTo(0, current);

                    if (Math.abs(target - current) < 0.6) {
                        ticking = false;
                        return;
                    }
                    requestAnimationFrame(animate);
                }

                window.addEventListener('wheel', (event) => {
                    const inModal = document.querySelector('.modal.show');
                    if (inModal) return;
                    event.preventDefault();

                    const delta = Math.max(-maxStep, Math.min(maxStep, event.deltaY));
                    target = Math.max(0, target + delta);

                    if (!ticking) {
                        current = window.scrollY;
                        requestAnimationFrame(animate);
                    }
                }, { passive: false });

                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        current = window.scrollY;
                        target = window.scrollY;
                    }
                }, { passive: true });
            }
            enableWheelSmooth();
            */

            if (backToTopBtn && sentinel) {
                if ('IntersectionObserver' in window) {
                    const io = new IntersectionObserver((entries) => {
                        const isTopVisible = entries[0].isIntersecting;
                        backToTopBtn.style.display = isTopVisible ? 'none' : 'flex';
                    }, { threshold: 0 });

                    io.observe(sentinel);
                } else {
                    function toggleBackToTop(){
                        const y = window.pageYOffset || document.documentElement.scrollTop || 0;
                        backToTopBtn.style.display = (y > 200) ? 'flex' : 'none';
                    }
                    window.addEventListener('scroll', toggleBackToTop, { passive: true });
                    window.addEventListener('resize', toggleBackToTop, { passive: true });
                    toggleBackToTop();
                }

                backToTopBtn.addEventListener('click', () => {
                    smoothScrollTo(0, 780);
                });
            }

            const revealTargets = document.querySelectorAll('.home-landing .reveal');
            if (revealTargets.length) {
                if ('IntersectionObserver' in window) {
                    const revealObserver = new IntersectionObserver((entries, observer) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) return;
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        });
                    }, { threshold: 0.12 });

                    revealTargets.forEach((el) => revealObserver.observe(el));
                } else {
                    revealTargets.forEach((el) => el.classList.add('is-visible'));
                }
            }
        }

        function initKakaoMap(){
            const mapContainer = document.getElementById('kakao-map');
            if (!mapContainer || !window.kakao || !window.kakao.maps) return;

            const lat = parseFloat("{{ env('SITE_MAP_LAT') }}") || 37.3032595;
            const lng = parseFloat("{{ env('SITE_MAP_LNG') }}") || 126.7381093;

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
            const $errorFields = $('#error-name, #error-contact_method, #error-phone, #error-email, #error-inquire_memo, #error-personal_info_agree');
            const $invalidInputs = $('#name, #phone, #email, #inquire_memo');
            const payload = {
                name: $('#name').val(),
                contact_method: $('input[name="contact_method"]:checked').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                inquire_memo: $('#inquire_memo').val(),
                personal_info_agree: $('#personal_info_agree').is(':checked') ? 1 : 0,
            };

            $errors.addClass('d-none').text('');
            $errorFields.addClass('d-none').text('');
            $invalidInputs.removeClass('is-invalid');
            $('input[name="contact_method"]').removeClass('is-invalid');
            $('#personal_info_agree').removeClass('is-invalid');

            $.ajax({
                method: 'POST',
                url: $form.attr('action'),
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                beforeSend: function () {
                    if (typeof window.showLoading === 'function') {
                        window.showLoading();
                    }
                },
            }).done(function() {
                $('#contactConfirmModal').modal('hide');
                $('#contactModal').modal('hide');
                if (typeof window.hideLoading === 'function') {
                    window.hideLoading();
                }
            }).fail(function(xhr) {
                const res = xhr.responseJSON || {};
                const errors = res.errors || {};
                const messages = Object.values(errors).flat();
                if (messages.length) {
                    const fieldMap = {
                        name: '#name',
                        contact_method: 'input[name="contact_method"]',
                        phone: '#phone',
                        email: '#email',
                        inquire_memo: '#inquire_memo',
                        personal_info_agree: '#personal_info_agree',
                    };
                    const errorMap = {
                        name: '#error-name',
                        contact_method: '#error-contact_method',
                        phone: '#error-phone',
                        email: '#error-email',
                        inquire_memo: '#error-inquire_memo',
                        personal_info_agree: '#error-personal_info_agree',
                    };

                    $.each(errors, function(field, fieldMessages){
                        const selector = fieldMap[field];
                        const errorSelector = errorMap[field];
                        if (selector) {
                            $(selector).addClass('is-invalid');
                        }
                        if (errorSelector) {
                            $(errorSelector).removeClass('d-none').text(fieldMessages.join(' '));
                        }
                    });
                } else {
                    $errors.removeClass('d-none').text('문의사항 등록 실패 사유를 확인해주세요.');
                }
            });
        }

        function updateContactMethodVisibility(){
            const $phoneGroup = $('#contactPhoneGroup');
            const $emailGroup = $('#contactEmailGroup');
            const method = $('input[name="contact_method"]:checked').val();
            
            if (method === 'email') {
                $emailGroup.removeClass('d-none');
                $phoneGroup.addClass('d-none');
            } else {
                $phoneGroup.removeClass('d-none');
                $emailGroup.addClass('d-none');
            }
        }
    </script>
@endsection
