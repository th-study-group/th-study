@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">상세내역</h2>
                    <p class="text-secondary small mb-0">문의 내용을 확인할 수 있습니다.</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">제목</span>
                    <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $post->title }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">작성자</span>
                    <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $post->user?->nick_name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">내용</span>
                    <div class="board-field board-content bg-light rounded-3 px-3 py-2" style="min-height: 240px; max-height: 420px; overflow: auto; scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="board-content-text">{{ $post->content }}</div>
                    </div>
                </div>
                <div class="board-meta d-flex flex-wrap gap-2 text-secondary small align-items-baseline">
                    <span>등록시각: {{ $post->create_datetime }}</span>
                    @if (!empty($post->update_datetime))
                        <span class="text-danger">(수정시각: {{ $post->update_datetime }})</span>
                    @endif
                    <span class="ms-auto text-nowrap">진행상태:</span>
                    @php
                        $statusKey = $post->status ?? 'wait';
                        $badgeClass = $statusBadgeClasses[$statusKey] ?? 'secondary';
                        $statusLabel = $statusList[$statusKey] ?? $statusKey;
                    @endphp
                    <span class="badge text-bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>
            </div>

        </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex gap-2 ms-auto">
                @if ($post->user_idx === auth()->id() && ($post->status ?? 'wait') === 'wait')
                    <button type="button" id="btn_post_modify" class="btn btn-outline-secondary">수정</button>
                    <button type="button" id="btn_post_delete" class="btn btn-outline-danger">삭제</button>
                @endif
                <button type="button" id="btn_post_list" class="btn btn-secondary">목록</button>
            </div>
        </div>

        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm mt-3">
            @include('comments.show')
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            const editUrl = "{{ route('inquiries.edit', ['idx' => $post->idx]) }}";
            const deleteUrl = "{{ route('inquiries.soft.delete', ['idx' => $post->idx]) }}";
            const listUrl = "{{ route('inquiries.index') }}";

            $("#btn_post_list").on("click", function() {
                location.href = listUrl; 
            });

            $('#btn_post_modify').on('click', function(){
                if (!confirm('수정하시겠습니까?')) {
                    return;
                }
                location.href = editUrl;
            });

            $('#btn_post_delete').on('click', function(){
                if (!confirm('삭제하시겠습니까?')) {
                    return;
                }
                requestAjax({
                    method: 'DELETE',
                    url: deleteUrl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    onSuccess: function () {
                        alert('문의가 삭제되었습니다.');
                        location.href = listUrl;
                    },
                    onError: function (xhr) {
                        let message = '삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    },
                });
            });
        });
    </script>
@endsection
