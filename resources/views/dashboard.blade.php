@extends('layouts.app')

@section('title', '대시보드')

@section('content')
<div class="col-12">
    <div class="d-flex flex-column gap-4">
        <div class="card border-0 shadow-sm text-bg-dark">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge rounded-pill text-bg-light text-dark mb-2">로그인 완료</span>
                        <h1 class="h4 mb-2">태희님, 환영합니다</h1>
                        <p class="mb-0 text-white-50">프로필과 최근 활동을 한눈에 확인할 수 있는 대시보드입니다.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill text-bg-light text-dark">이번 달 문의 12건</span>
                        <span class="badge rounded-pill text-bg-light text-dark">등급: 일반</span>
                    </div>
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
                            <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal" aria-label="알림">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                            </button>
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
                                <div class="fw-semibold fs-5">태희</div>
                                <div class="text-muted">등급: 일반 회원</div>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">최초 가입일</div>
                                    <div class="fw-semibold">2024.01.15</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">문의내역</div>
                                    <div class="fw-semibold">7건</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded-3 p-2 bg-light">
                                    <div class="small text-muted">작성글</div>
                                    <div class="fw-semibold">14개</div>
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
                                <a href="{{ route('users.edit', ['idx' => $accountIdx]) }}" class="btn btn-outline-primary btn-sm">
                                    내정보변경
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small mb-1">최근 활동</div>
                                <div class="fw-semibold mb-3">게시판 최신글</div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>자유게시판 글이 없습니다</div>
                                    <span class="text-muted small">방금</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>공지사항 글이 없습니다</div>
                                    <span class="text-muted small">오늘</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>문의게시판 글이 없습니다</div>
                                    <span class="text-muted small">이번 주</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted small mb-1">알림 요약</div>
                                <div class="fw-semibold mb-3">읽지 않은 알림</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="display-6 fw-bold">3</div>
                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#notificationModal">알림 확인</button>
                                </div>
                                <div class="text-muted small mt-2">새 답변과 공지 알림을 실시간으로 보여줄 영역입니다.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <h4 class="mb-0">게시판 최신글</h4>
            <span class="text-muted small">새 글이 없으면 “없음”으로 표시됩니다.</span>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">자유게시판</div>
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
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">공지사항</div>
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
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="fw-semibold mb-3">문의게시판</div>
                        <div class="list-group list-group-flush border rounded-3">
                            <div class="list-group-item">
                                <div class="fw-semibold text-truncate">결제 오류 문의드립니다</div>
                                <div class="small text-muted">답변 대기</div>
                            </div>
                        </div>
                        <a href="#" class="text-decoration-none d-inline-flex align-items-center gap-1 small mt-3 text-primary">바로가기 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
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
        </div>
    </div>
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
