@extends('layouts.app')

@section('title', '대시보드')

@section('content')
@push('styles')
<style>
    .dashboard-empty-card {
        border: 1px solid #dbe7f5;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f8fbff, #fff 58%, #eef5ff);
    }
    .dashboard-empty-card .empty-visual {
        position: relative;
        height: 74px;
        overflow: hidden;
        border: 1px solid #e0eaf6;
        border-radius: .8rem;
        background: repeating-linear-gradient(0deg, transparent 0 17px, #e7eef8 18px), repeating-linear-gradient(90deg, transparent 0 39px, #e7eef8 40px);
    }
    .dashboard-empty-card .empty-visual::after {
        position: absolute;
        right: 18%;
        bottom: 21px;
        left: 18%;
        height: 28px;
        border-top: 3px solid #78aef0;
        border-radius: 50%;
        content: '';
        transform: skewY(-8deg);
    }
    .dashboard-empty-card .empty-dot {
        display: inline-block;
        width: .5rem;
        height: .5rem;
        margin-right: .5rem;
        border-radius: 50%;
        background: #377de0;
        box-shadow: 1rem 0 #b9d5f5, 2rem 0 #e0ebf8;
    }
    .dashboard-empty-card .empty-icon { color: #377de0; font-size: 1.4rem; }
</style>
@endpush

<div class="col-12">
    <div class="d-flex flex-column gap-4">
        <div class="card border-0 shadow-sm text-bg-dark">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge rounded-pill text-bg-light text-dark mb-2">로그인 완료</span>
                        <h1 class="h4 mb-2">{{ auth()->user()->name ?? 'NONE' }}님, 환영합니다</h1>
                        <p class="mb-0 text-white-50">프로필과 최근 활동을 한눈에 확인할 수 있는 대시보드입니다.</p>
                    </div>
                    {{--
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill text-bg-light text-dark">이번 달 문의 -건</span>
                        <span class="badge rounded-pill text-bg-light text-dark">등급: 일반</span>
                    </div>
                    --}}
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">프로필</h5>
                                <span class="badge rounded-pill text-bg-primary">MEMBER</span>
                            </div>
                            {{--
                            <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal" aria-label="알림">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                            </button>
                            --}}
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img
                                class="rounded-circle border border-3 border-light shadow-sm"
                                width="96"
                                height="96"
                                alt="기본 프로필 이미지"
                                src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='%23dbeafe'/><stop offset='1' stop-color='%23e0f2fe'/></linearGradient></defs><rect width='160' height='160' rx='80' fill='url(%23g)'/><circle cx='80' cy='66' r='34' fill='%2393c5fd'/><rect x='36' y='106' width='88' height='36' rx='18' fill='%2393c5fd'/></svg>"
                            />
                            <div>
                                <div class="fw-semibold fs-5">{{ auth()->user()->nick_name ?? 'NONE' }}</div>
                                <div class="text-muted">등급: {{ auth()->user()->level_label }}</div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">최초 가입일</div>
                                    <div class="fw-semibold">{{ auth()->user()->create_datetime->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">문의내역</div>
                                    <div class="fw-semibold text-primary">활동 기록</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">작성글</div>
                                    <div class="fw-semibold text-primary">콘텐츠 기록</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex flex-column flex-lg-row align-items-start justify-content-between gap-2">
                                <div>
                                    <h5 class="mb-1">요약</h5>
                                    <div class="text-muted">프로필 주변 핵심 정보를 한눈에 확인하세요.</div>
                                </div>
                                <a href="{{ route('users.account.edit') }}" class="btn btn-outline-primary btn-sm">
                                    내정보변경
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small mb-1">홍보</div>
                                <div class="fw-semibold mb-3">공지 · 이벤트 · 신규기능</div>
                                
                                {{--
                                    예시)
                                    운영 정책 개편안내 | 3/20
                                    봄맞이 활동 이베트 진행 | 이벤트 상세 링크
                                    문의 히스토리 필터 기능 추가 | 업데이트 
                                --}}
                                <div class="dashboard-empty-card rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold">사이트 소식이 모이는 공간</div>
                                            <div class="small text-muted mt-1">새로운 안내와 변화가 이곳에 기록됩니다.</div>
                                        </div>
                                        <i class="bi bi-megaphone text-primary fs-4" aria-hidden="true"></i>
                                    </div>
                                    <div class="empty-visual mt-3" aria-hidden="true"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small mb-1">알림 요약</div>
                                <div class="fw-semibold mb-3">읽지 않은 알림</div>
                                
                                <div class="dashboard-empty-card rounded-3 p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-chat-square-text text-primary fs-3" aria-hidden="true"></i>
                                        <div>
                                            <div class="fw-semibold">문의 기록을 한곳에서</div>
                                            <div class="small text-muted mt-1">문의와 답변의 흐름을 확인합니다.</div>
                                        </div>
                                    </div>
                                    <div class="mt-4 small text-muted"><span class="empty-dot" aria-hidden="true"></span>문의 접수 · 확인 · 답변</div>
                                </div>
                                {{-- 알림 데이터 연결 시 아래 버튼과 모달을 다시 활성화합니다.
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal">알림 확인</button>
                                --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <h4 class="mb-0">게시판 최신글</h4>
                            <span class="text-muted small">나의 기록과 사이트 소식을 한눈에 확인합니다.</span>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">공지사항</div>
                        <div class="list-group list-group-flush border rounded-3">
                            {{-- 실제 공지사항 목록 출력 영역은 데이터 연결 시 아래 구조로 복원합니다. --}}
                            <div class="dashboard-empty-card rounded-3 p-3">
                                <div class="fw-semibold">새로운 소식이 모이는 공간</div>
                                <div class="small text-muted mt-1">사이트의 중요한 안내를 이곳에서 확인합니다.</div>
                                <div class="empty-visual mt-3" aria-hidden="true"></div>
                            </div>
                        </div>
                        {{--
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">3월 시스템 점검 안내</div>
                                <div class="small text-muted">오늘 · 조회 124</div>
                            </div>
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">서비스 이용약관 개정</div>
                                <div class="small text-muted">어제 · 조회 98</div>
                            </div>
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">2월 업데이트 요약</div>
                                <div class="small text-muted">2일 전 · 조회 210</div>
                            </div>
                        </div>
                        --}}
                        <a href="{{ route('posts.index', ['post_type' => 'notice']) }}" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">문의게시판</div>
                        <div class="list-group list-group-flush border rounded-3">
                            {{-- 실제 문의 목록 출력 영역은 데이터 연결 시 아래 구조로 복원합니다. --}}
                            <div class="dashboard-empty-card rounded-3 p-3">
                                <div class="fw-semibold">문의와 답변의 흐름</div>
                                <div class="small text-muted mt-1">문의 기록이 쌓이면 이곳에서 확인합니다.</div>
                                <div class="mt-3 small text-muted"><span class="empty-dot" aria-hidden="true"></span>접수 · 확인 · 답변</div>
                            </div>
                        </div>
                        {{--
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">결제 오류 문의드립니다</div>
                                <div class="small text-muted">답변 대기</div>
                            </div>
                        </div>
                        --}}
                        <a href="{{ route('inquiries.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">콘텐츠게시판</div>
                        <div class="list-group list-group-flush border rounded-3">
                            {{-- 실제 콘텐츠 목록 출력 영역은 데이터 연결 시 아래 구조로 복원합니다. --}}
                            <div class="dashboard-empty-card rounded-3 p-3">
                                <div class="fw-semibold">나의 콘텐츠 기록</div>
                                <div class="small text-muted mt-1">작성한 글과 콘텐츠가 이곳에 모입니다.</div>
                                <div class="empty-visual mt-3" aria-hidden="true"></div>
                            </div>
                        </div>
                        {{--
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">주말 번개모임 후기 공유합니다</div>
                                <div class="small text-muted">5분 전 · 댓글 2</div>
                            </div>
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">신규 기능 의견 수렴합니다</div>
                                <div class="small text-muted">1시간 전 · 댓글 5</div>
                            </div>
                        </div>
                        --}}
                        <a href="{{ route('blogs.index') }}" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            {{--
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">자료실</div>
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item text-muted text-center">등록된 글이 없습니다.</div>
                        </div>
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">이벤트</div>
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">봄맞이 출석 이벤트</div>
                                <div class="small text-muted">D-3</div>
                            </div>
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">리뷰 작성 챌린지</div>
                                <div class="small text-muted">D-7</div>
                            </div>
                        </div>
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">FAQ</div>
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">비밀번호 변경은 어떻게 하나요?</div>
                                <div class="small text-muted">자주 묻는 질문</div>
                            </div>
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">이메일 인증이 안돼요</div>
                                <div class="small text-muted">자주 묻는 질문</div>
                            </div>
                        </div>
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            --}}
        </div>
    </div>
</div>

<div class="dashboard-bottom-multiplex-ad text-center my-3">
    <x-adsense
        class="dashboard-bottom-multiplex-ad-slot"
        :ad-slot="config('adsense.units.common_bottom_multiplex.ad_slot')"
        :format="config('adsense.units.common_bottom_multiplex.format')" />
</div>

<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">알림</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">관리자 답변이 도착했습니다</div>
                        <div class="small text-muted">문의게시판 · 5분 전</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">새 공지사항이 등록되었습니다</div>
                        <div class="small text-muted">공지사항 · 1시간 전</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">시스템 점검 예정 안내</div>
                        <div class="small text-muted">공지사항 · 어제</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">문의 게시글에 새 댓글이 달렸습니다</div>
                        <div class="small text-muted">문의게시판 · 어제</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">이벤트가 시작되었습니다</div>
                        <div class="small text-muted">이벤트 · 2일 전</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">관리자 답변이 도착했습니다</div>
                        <div class="small text-muted">문의게시판 · 5분 전</div>
                    </a>
                    <a class="list-group-item list-group-item-action" href="#">
                        <div class="fw-semibold">새 공지사항이 등록되었습니다</div>
                        <div class="small text-muted">공지사항 · 1시간 전</div>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <span class="text-muted small">스크롤/페이지 처리 예정</span>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        window.requestAnimationFrame(function () {
            document.querySelectorAll('.dashboard-bottom-multiplex-ad .adsbygoogle').forEach(function (ad) {
                if (ad.dataset.adsensePushQueued === 'true' || ad.dataset.adsbygoogleStatus || ad.offsetWidth <= 0) {
                    return;
                }

                ad.dataset.adsensePushQueued = 'true';

                try {
                    (window.adsbygoogle = window.adsbygoogle || []).push({});
                } catch (error) {
                    console.warn('AdSense dashboard multiplex ad initialization failed.', error);
                }
            });
        });
    </script>
@endsection
