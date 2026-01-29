@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">문의 상세</h2>
                    <p class="text-secondary small mb-0">문의 내용을 확인해 주세요.</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">제목</span>
                    <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">문의 제목 더미 텍스트가 길어질 때 말줄임 처리 확인을 위한 더미 제목입니다</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">작성자</span>
                    <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">홍길동(작성자 이름이 길어질 수 있는 경우를 위한 더미 텍스트)</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">내용</span>
                    <div class="board-field board-content bg-light rounded-3 px-3 py-2">
                        <div class="board-content-text">문의 내용 더미 텍스트 영역입니다. 문의 내용 더미 텍스트 영역입니다.</div>
                    </div>
                </div>
                <div class="board-meta d-flex flex-wrap gap-2 text-secondary small align-items-baseline">
                    <span>등록시각: 2026-01-29 10:20</span>
                    <span class="text-danger">(수정시각: 2026-01-29 11:05)</span>
                    <span class="ms-auto text-nowrap">진행상태:</span>
                    <span class="badge text-bg-warning">처리중</span>
                </div>
            </div>

        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary">수정</button>
                <button type="button" class="btn btn-outline-danger">삭제</button>
                <a href="{{ route('inquiries.index') }}" class="btn btn-secondary">목록</a>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <select class="form-select form-select-sm board-status-select">
                    <option>접수</option>
                    <option selected>처리중</option>
                    <option>답변완료</option>
                </select>
                <button type="button" class="btn btn-primary btn-sm">상태변경</button>
            </div>
        </div>

        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm mt-3">
            @include('comments.show')
        </div>
    </section>
@endsection
