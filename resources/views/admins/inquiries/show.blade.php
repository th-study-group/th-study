@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">상세내역</h2>
                    <p class="text-secondary small mb-0">고객 문의 내용을 확인할 수 있습니다.</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">제목</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->title }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">작성자</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->user?->nick_name ?? $post->user?->name ?? '-' }}</div>
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
                    <span id="inquiry_status_badge" class="badge text-bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>
            </div>

        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mt-3 board-status-actions">
            @can('inquiryUpdateStatus', $post)
                <select id="status" name="status" class="form-select form-select-sm board-status-select">
                    @foreach ($statusList ?? [] as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(($post->status ?? 'wait') === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <button type="button" id="btn_change_status" class="btn btn-primary">상태변경</button>
            @endcan
            @can('inquiryDelete', $post)
                <button type="button" id="btn_post_delete" class="btn btn-outline-danger">삭제</button>
            @endcan
            <button type="button" id="btn_post_list" class="btn btn-secondary">목록</button>
        </div>

        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm mt-3">
            @include('admins.comments.show')
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            const listUrl = "{{ route('admins.inquiries.index') }}";
            const deleteUrl = "{{ route('admins.inquiries.soft.delete', ['idx' => $post->idx]) }}";
            const statusUrl = "{{ route('admins.inquiries.status.update', ['idx' => $post->idx]) }}";
            const showUrl = "{{ route('admins.inquiries.show', ['idx' => $post->idx]) }}";

            $('#btn_post_list').on('click', function() {
                location.href = listUrl;
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

            $('#btn_change_status').on('click', function(){
                if (!confirm('상태를 변경하시겠습니까?')) {
                    return;
                }

                requestAjax({
                    method: 'PATCH',
                    url: statusUrl,
                    dataType: 'json',
                    data: {
                        status: $('#status').val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    onSuccess: function (res) {
                        alert('진행상태가 변경되었습니다.');
                        location.href = showUrl;
                    },
                    onError: function (xhr) {
                        let message = '상태 변경 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
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
