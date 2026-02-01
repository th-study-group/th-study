<div class="modal" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
                <div class="fw-semibold small">잠시만 기다려 주세요.</div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex align-items-center w-100 d-lg-none">
            @if (request()->route('hideSide'))
                <a
                    class="navbar-toggler border-0 text-decoration-none"
                    href="{{ config('app.url') }}"
                    aria-label="Go to home">
                    <span class="fw-bold fs-4 t-mark" aria-hidden="true">TH</span>
                </a>
            @else
                <button 
                    class="navbar-toggler border-0" 
                    type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar"
                    aria-label="Toggle sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif
            <a class="navbar-brand fw-semibold mx-auto mb-0 text-decoration-none" href="{{ config('app.url') }}">
                {{ config('app.name') }}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNav" aria-controls="topNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <a class="d-none d-lg-inline-flex align-items-center text-decoration-none me-3 text-white" href="{{ config('app.url') }}" aria-label="Go to home">
            <span class="fw-bold fs-5 t-mark" aria-hidden="true">TH</span>
        </a>
        
        <a class="navbar-brand fw-semibold d-none d-lg-inline-flex text-decoration-none" href="{{ config('app.url') }}">
            {{ config('app.name') }}
        </a>
        
        <div class="collapse navbar-collapse mt-3 mt-lg-0" id="topNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown nav-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">소개</a>
                    <ul class="dropdown-menu">
                         <li><a class="dropdown-item" href="{{ route('home') }}">사이트소개</a></li>
                         <li><a class="dropdown-item" href="{{ route('posts.index', ['post_type' => 'notice']) }}">공지사항</a></li>
                    </ul>
                </li>
                {{--
                <li class="nav-item"><a class="nav-link" href="{{ route('photos.index') }}">사진관리</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('videos.index') }}">영상관리</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('blogs.index') }}">정보관리</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('maps.index') }}">장소관리</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('documents.index') }}">문서관리</a></li>
                --}}
                <li class="nav-item"><a class="nav-link" href="{{ route('blogs.index') }}">정보관리</a></li>
                @auth
                    @if (auth()->user()?->email_verify_datetime)
                    <li class="nav-item dropdown nav-dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">마이페이지</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">대시보드</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.account.edit') }}">내 정보 변경</a></li>
                            <li><a class="dropdown-item" href="{{ route('inquiries.index') }}">나의 문의 내역</a></li>
                        </ul>
                    </li>

                        @if (auth()->user()?->level === 'admin')
                        <li class="nav-item dropdown nav-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">관리자</a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admins.posts.index', ['post_type' => 'notice']) }}">공지사항</a></li>
                                <li><a class="dropdown-item" href="{{ route('admins.members.index') }}">회원현황</a></li>
                                <li><a class="dropdown-item" href="{{ route('admins.inquiries.index') }}">문의내역</a></li>
                            </ul>
                        </li>
                        @endif
                    @endif
                @endauth
            </ul>
        
            @guest
            <div class="dropdown auth-dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center auth-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Start
                </button>
                <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>로그인</a></li>
                <li><a class="dropdown-item" href="{{ route('register.form') }}"><i class="bi bi-person-plus me-2" aria-hidden="true"></i>회원가입</a></li>
                </ul>
            </div>
            @endguest

            @auth
                @if (auth()->user()?->email_verify_datetime)
                <a class="btn btn-outline-light d-flex align-items-center auth-btn" href="{{ route('logout') }}">
                    <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>로그아웃
                </a>
                @endif
            @endauth
        </div>
    </div>
</nav>
