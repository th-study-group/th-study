@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <form id="guest_post_form" method="POST" action="{{ route('admins.guest_posts.update', ['post_type' => 'inquiries', 'idx' => $idx]) }}">
            @csrf
            @method('PATCH')
            <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
                <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                    <div>
                        <h2 class="board-title h5 mb-1">문의하기</h2>
                        <p class="text-secondary small mb-0">홈페이지 상담 문의 내용을 확인할 수 있습니다.</p>
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
                        <div class="board-field board-content bg-light rounded-3 px-3 py-2" style="min-height: 240px; max-height: 420px; overflow: auto; scrollbar-width: none; -ms-overflow-style: none;">
                            <div class="board-content-text">문의 내용 더미 텍스트 영역입니다.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">처리결과</span>
                        <textarea id="memo"
                                  name="memo"
                                  class="form-control board-textarea bg-light rounded-3 px-3 py-2 border-0"
                                  rows="6"
                                  placeholder="처리결과를 입력해 주세요"></textarea>
                    </div>
                    <div class="board-meta d-flex flex-wrap gap-2 text-secondary small align-items-baseline">
                    <span>등록시각: 2026-01-29 10:20:22</span>
                        <span class="ms-auto text-nowrap">진행상태:</span>
                        <span class="badge text-bg-warning">처리중</span>
                    </div>
                </div>

            </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <select id="status" name="status" class="form-select form-select-sm board-status-select">
                    @foreach ($statusList ?? [] as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <button type="button" id="btn_post_save" class="btn btn-primary">적용</button>
                <button type="button" id="btn_post_delete" class="btn btn-outline-danger">삭제</button>
                <button type="button" id="btn_post_list" class="btn btn-secondary">목록</button>
            </div>
        </div>
        </form>
    </section>
@endsection

@section('script')
    <script>
        $(function(){

            const listUrl = "{{ route('admins.guest_posts.index', ['post_type' => 'inquiries']) }}";

            $("#btn_post_list").on("click", function() {
                location.href = listUrl;
            });

            $("#btn_post_save").on("click", function() {
                if (!confirm('적용하시겠습니까?')) {
                    return;
                }
            });

            $('#btn_post_delete').on('click', function(){
                if (!confirm('삭제하시겠습니까?')) {
                    return;
                }
            });
        });
    </script>
@endsection
