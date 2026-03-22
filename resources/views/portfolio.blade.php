@extends('layouts.app')

@section('title', '포트폴리오')
@section('og_description', '개발자성장플랫폼 티에이치스터디 포트폴리오')

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
                    <h1 class="hero-title fw-bold mb-3">Laravel 중심 서비스에 <span class="accent">FastAPI 학습 흐름</span>까지 확장한 포트폴리오</h1>
                    <p class="fs-5 text-white-50 mb-2 hero-kicker">개발자 성장 플랫폼</p>
                    <p class="lead text-white-50 mb-4 hero-sub">
                        기능 구현, 문서 정리, 구조 학습을 반복하면서 PHP/Laravel 중심 서비스에 Python/FastAPI 기초 역량을 연결해 가는 과정을 정리합니다.
                    </p>
                    <div class="d-flex flex-wrap gap-3 no-print">
                        <a class="btn btn-primary btn-lg rounded-4 px-4" href="https://www.th-study.com/" target="_blank" rel="noreferrer">
                            <i class="bi bi-globe2 me-2"></i>사이트
                        </a>
                        <a class="btn btn-outline-light btn-lg rounded-4 px-4" href="https://github.com/th-study-group/th-study" target="_blank" rel="noreferrer">
                            <i class="bi bi-github me-2"></i>GitHub
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-card p-4">
                        <div class="fw-bold text-white mb-2">핵심 요약</div>
                        <ul class="text-white-50 mb-3" style="margin-left:18px;">
                            <li>Laravel 기반 웹 서비스를 직접 설계하고 화면, 기능, 문서를 함께 관리</li>
                            <li>Controller-Service-Repository 구조로 기능 분리</li>
                            <li>FastAPI는 별도 학습 주제로 설치, 가상환경, 기본 라우팅부터 정리</li>
                            <li>포트폴리오에는 공개 가능한 수준의 기술 개요만 노출</li>
                        </ul>
                        <div class="fw-bold text-white mb-2">Tech Stack</div>
                        <div class="d-flex flex-wrap gap-3 align-items-center icons">
                            <i class="devicon-php-plain"></i>
                            <i class="devicon-laravel-original"></i>
                            <i class="devicon-python-plain"></i>
                            <i class="devicon-fastapi-plain"></i>
                            <i class="devicon-mysql-original"></i>
                            <i class="devicon-bootstrap-plain"></i>
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
                    <p class="leadx mb-0">공개용 요약 중심으로 정리했습니다.</p>
                </div>
                <div class="col-lg-8">
                    <div class="box pad toc">
                        <ul>
                            <li><a href="#overview">1. 프로젝트 개요</a><span class="toc-dots"></span><span class="toc-desc">방향과 목표</span></li>
                            <li><a href="#stack">2. 기술 스택</a><span class="toc-dots"></span><span class="toc-desc">주요 사용 기술</span></li>
                            <li><a href="#architecture">3. 구조 설계</a><span class="toc-dots"></span><span class="toc-desc">레이어 분리 기준</span></li>
                            <li><a href="#features">4. 주요 구현 기능</a><span class="toc-dots"></span><span class="toc-desc">사용자 기능과 콘텐츠</span></li>
                            <li><a href="#fastapi">5. FastAPI 학습</a><span class="toc-dots"></span><span class="toc-desc">설치와 기본 API</span></li>
                            <li><a href="#docs">6. 문서화 기준</a><span class="toc-dots"></span><span class="toc-desc">README와 포트폴리오 분리</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="overview" class="section bg-light">
        <div class="container">
            <h2 class="h2x mb-3">1. 프로젝트 개요</h2>
            <div class="box pad">
                <p class="leadx mb-3">
                    티에이치스터디는 기록, 게시판, 문의, 사용자 기능을 직접 만들며 웹 서비스를 구조적으로 운영하는 감각을 쌓기 위한 개인 개발 플랫폼입니다.
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold">서비스 관점</div>
                            <div class="text-secondary mt-1">기능을 만드는 데서 끝내지 않고 흐름과 역할을 분리해 관리</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold">학습 관점</div>
                            <div class="text-secondary mt-1">Laravel 중심 기반 위에 Python/FastAPI까지 확장</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold">문서 관점</div>
                            <div class="text-secondary mt-1">README는 학습용, 포트폴리오는 공개용 요약으로 분리</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="stack" class="section">
        <div class="container">
            <h2 class="h2x mb-3">2. 기술 스택</h2>
            <div class="box pad">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:24%">영역</th>
                                <th>기술</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Backend</td>
                                <td>PHP 8.2, Laravel 10</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Frontend</td>
                                <td>Blade, Bootstrap 5, jQuery</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Database</td>
                                <td>MySQL</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Python Study</td>
                                <td>Python 3, venv, FastAPI, Uvicorn</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Development</td>
                                <td>GitHub, Docker Compose 기반 로컬 검증</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="callout mt-4">
                    <strong>핵심 포인트</strong><br>
                    Laravel을 메인 서비스 스택으로 사용하고, FastAPI는 마이크로서비스 감각과 Python 웹 개발 기초를 익히는 학습 트랙으로 분리해 정리하고 있습니다.
                </div>
            </div>
        </div>
    </section>

    <section id="architecture" class="section bg-light">
        <div class="container">
            <h2 class="h2x mb-3">3. 구조 설계</h2>
            <div class="box pad">
                <p class="leadx mb-3">기능이 늘어나도 흐름을 따라가기 쉽도록 역할을 레이어 단위로 분리했습니다.</p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:22%">레이어</th>
                                <th>역할</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Route / Controller</td>
                                <td>요청 진입점과 화면 또는 응답 연결</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">FormRequest</td>
                                <td>입력값 검증과 요청 데이터 정리</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Service</td>
                                <td>도메인 규칙과 처리 흐름 담당</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Repository</td>
                                <td>DB 조회와 저장 책임 분리</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Policy / Middleware</td>
                                <td>접근 제어와 권한 정책 관리</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="section">
        <div class="container">
            <h2 class="h2x mb-3">4. 주요 구현 기능</h2>
            <div class="box pad">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">사용자 기능</div>
                            <ul class="mb-0" style="margin-left:18px;">
                                <li>회원가입, 로그인, 이메일 인증</li>
                                <li>비밀번호 재설정 및 계정 관리</li>
                                <li>사용자별 접근 제어와 상태 검증</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">콘텐츠 기능</div>
                            <ul class="mb-0" style="margin-left:18px;">
                                <li>공지, 블로그, 문의, 댓글 흐름 구현</li>
                                <li>관리자/사용자 역할 분리</li>
                                <li>기능 확장을 고려한 구조화된 코드 정리</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">프론트 구성</div>
                            <ul class="mb-0" style="margin-left:18px;">
                                <li>Blade 템플릿 기반 페이지 구성</li>
                                <li>Bootstrap 5 중심 UI 정리</li>
                                <li>화면별 스크립트 분리</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">문서화</div>
                            <ul class="mb-0" style="margin-left:18px;">
                                <li>기술 배경과 구조를 README로 기록</li>
                                <li>공개용 설명은 포트폴리오로 요약</li>
                                <li>민감한 운영 세부는 공개 문서에서 제외</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fastapi" class="section bg-light">
        <div class="container">
            <h2 class="h2x mb-3">5. FastAPI 학습</h2>
            <div class="box pad">
                <p class="leadx mb-3">
                    FastAPI는 Python 기반 API 개발 흐름을 익히기 위한 학습 주제로 정리했습니다. 공개 포트폴리오에는 로컬 개발 기준의 핵심만 담고, 상세한 설치 메모와 예제 코드는 README에서 관리합니다.
                </p>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">학습 포인트</div>
                            <ul class="mb-0" style="margin-left:18px;">
                                <li><code>python3</code> 기반 실행 습관 정리</li>
                                <li><code>python3 -m pip</code> 기준 패키지 설치</li>
                                <li><code>venv</code> 기반 프로젝트 격리</li>
                                <li>기본 라우팅과 JSON 응답 구조 이해</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-3 border rounded-4 h-100">
                            <div class="fw-bold mb-2">기본 예제</div>
<pre class="mb-0"><code>from fastapi import FastAPI

app = FastAPI()

@app.get("/")
def home():
    return {"message": "hello"}</code></pre>
                        </div>
                    </div>
                </div>
                <div class="callout mt-4">
                    <strong>정리 원칙</strong><br>
                    포트폴리오에는 학습 방향과 기술 키워드만 두고, 설치 순서와 실행 명령, 예제 코드는 README에 별도로 정리했습니다.
                </div>
            </div>
        </div>
    </section>

    <section id="docs" class="section">
        <div class="container">
            <h2 class="h2x mb-3">6. 문서화 기준</h2>
            <div class="box pad">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:24%">문서</th>
                                <th>목적</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">README.md</td>
                                <td>학습 메모, 설치 흐름, 기본 코드, 로컬 실행 방법 정리</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">포트폴리오</td>
                                <td>공개 가능한 프로젝트 개요, 기술 스택, 구조, 학습 방향만 요약</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="callout mt-4">
                    <strong>공개 정책</strong><br>
                    실제 운영 경로, 배포 절차, 백업 위치, 서비스 등록 정보 같은 세부 운영 정보는 포트폴리오에서 제외합니다.
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
