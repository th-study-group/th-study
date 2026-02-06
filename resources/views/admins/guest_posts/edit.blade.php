@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <form id="guest_post_form" name="guest_post_form" method="POST" action="{{ route('admins.guest_posts.update', ['post_type' => $postType, 'idx' => $post->idx]) }}">
            @csrf
            @method('PUT')
            <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
                <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                    <div>
                        <h2 class="board-title h5 mb-1">문의하기</h2>
                        <p class="text-secondary small mb-0">홈페이지 상담 문의 내용을 확인할 수 있습니다.</p>
                    </div>
                </div>

                <div class="mt-3">
                    @if ($errors->any())
                        <div class="alert alert-warning">에러가 있습니다. 확인해 주세요.</div>
                    @endif
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">제목</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $post->title }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">작성자</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $post->writer ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">연락처</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">
                            {{ ($post->contact_value ?? '') !== ''
                                ? preg_replace('/^(email|phone)\s+/i', '', $post->contact_value)
                                : '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">내용</span>
                        <div class="board-field board-content bg-light rounded-3 px-3 py-2" style="min-height: 240px;">
                            <div class="board-content-text">{{ $post->content }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">처리결과</span>
                        <textarea id="memo"
                                  name="memo"
                                  class="form-control board-textarea bg-light rounded-3 px-3 py-2 border-0 @error('memo') is-invalid @enderror"
                                  rows="6"
                                  placeholder="처리결과를 입력해 주세요">{{ old('memo', $post->memo ?? '') }}</textarea>
                        @error('memo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">개인정보방침 동의</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">
                            {{ $terms[(($post->personal_info_agree ?? 'N') === 'Y') ? 1 : 0] ?? '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">마케팅 동의</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">
                            {{ $terms[(($post->marketing_info_agree ?? 'N') === 'Y') ? 1 : 0] ?? '-' }}
                        </div>
                    </div>
                    <div class="board-meta d-flex flex-wrap gap-2 text-secondary small align-items-baseline">
                        <span>등록시각: {{ $post->create_datetime }}</span>
                        @if (!empty($post->update_datetime))
                            <span class="text-danger">(수정시각: {{ $post->update_datetime }})</span>
                        @endif
                        <span class="ms-auto text-nowrap">진행상태:</span>
                        <span id="guest_post_status_badge" class="badge text-bg-{{ $statusBadgeClasses[$post->status ?? 'wait'] ?? 'secondary' }}">
                            {{ $statusList[$post->status ?? 'wait'] ?? ($post->status ?? 'wait') }}
                        </span>
                    </div>
                </div>

            </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <select id="status" name="status" class="form-select form-select-sm board-status-select @error('status') is-invalid @enderror">
                    @foreach ($statusList ?? [] as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}" @selected(($post->status ?? 'wait') === $statusValue)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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

            const listUrl = "{{ route('admins.guest_posts.index', ['post_type' => $postType]) }}";
            const deleteUrl = "{{ route('admins.guest_posts.soft.delete', ['post_type' => $postType, 'idx' => $post->idx]) }}";

            $("#btn_post_list").on("click", function() {
                location.href = listUrl;
            });

            $("#btn_post_save").on("click", function() {
                if (!confirm('적용하시겠습니까?')) {
                    return;
                }
                $('#guest_post_form').submit();
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
                        alert('삭제되었습니다.');
                        location.href = listUrl;
                    },
                    onError: function (xhr) {
                        let message = '삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            //message = xhr.responseJSON.message;
                        }
                        alert(message);
                    },
                });
            });
        });
    </script>
@endsection
