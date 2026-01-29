@extends('layouts.app')

@section('title', '조회')

@section('content')
    <section class="col-12 col-lg-8">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="d-flex align-items-start justify-content-between gap-2 board-head-top">
                    <div class="flex-grow-1 board-min-w-0 board-head-text">
                        <h2 class="board-title h5 mb-1">나의 문의내역</h2>
                        <p class="text-secondary small mb-0 board-ellipsis-mobile">나중에 백엔드에서 전달되는 제목/설명이 들어갈 영역</p>
                    </div>
                    <a href="{{ route('inquiries.create') }}" class="btn btn-dark btn-sm text-nowrap">문의하기</a>
                </div>

                <div class="board-filter-box">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h3 class="board-filter-title small text-secondary mb-0">검색조건</h3>
                        <button class="board-filter-toggle btn btn-outline-secondary btn-sm collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#inquiryFilters"
                                aria-expanded="false"
                                aria-controls="inquiryFilters">
                            접기
                            <i class="bi bi-chevron-up ms-1"></i>
                        </button>
                    </div>

                    <div id="inquiryFilters" class="collapse show mt-2">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary mb-1">기간</label>
                                <div class="board-date-range">
                                    <input type="date" class="form-control form-control-sm">
                                    <span class="text-secondary small">~</span>
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary mb-1">상태</label>
                                <select class="form-select form-select-sm">
                                    <option>전체</option>
                                    <option>접수</option>
                                    <option>처리중</option>
                                    <option>답변완료</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-secondary mb-1">이름</label>
                                <input type="text" class="form-control form-control-sm" placeholder="이름">
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-primary">검색</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="board-table-wrap mt-3">
                <table class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 60px;">
                        <col style="width: 220px;">
                        <col class="board-col-hidden" style="width: 90px;">
                        <col style="width: 140px;">
                        <col class="board-col-hidden" style="width: 120px;">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">번호</th>
                            <th scope="col" class="text-nowrap">제목</th>
                            <th scope="col" class="board-col-hidden text-nowrap">상태</th>
                            <th scope="col" class="text-nowrap">등록일</th>
                            <th scope="col" class="board-col-hidden text-nowrap">답변일</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 
                            class="board-col-hidden" 적용하면 모바일에서 숨겨짐  
                            class="board-ellipsis" 적용 시 ... 으로 뒷부분 짤림
                        --}}
                        @for ($i = 20; $i >= 1; $i--)
                            <tr>
                                <td class="text-center text-nowrap">{{ $i }}</td>
                                <td class="text-start board-ellipsis">
                                    문의 제목 더미 {{ $i }} - 내용 미리보기 텍스트 영역
                                </td>
                                <td class="text-center text-nowrap board-col-hidden">
                                    @if ($i % 3 === 0)
                                        <span class="badge text-bg-success">답변완료</span>
                                    @elseif ($i % 3 === 1)
                                        <span class="badge text-bg-secondary">접수</span>
                                    @else
                                        <span class="badge text-bg-warning">처리중</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">2026-01-{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center text-nowrap board-col-hidden">2026-01-{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('inquiries.create') }}" class="btn btn-dark btn-sm text-nowrap">문의하기</a>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="문의내역 페이지네이션">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><span class="page-link">이전</span></li>
                    <li class="page-item active"><span class="page-link">1</span></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">다음</a></li>
                </ul>
            </nav>
        </div>
    </section>
@endsection
