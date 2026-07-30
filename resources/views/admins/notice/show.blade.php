@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0">공지사항 내용을 확인할 수 있습니다.</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">제목</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->title }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">작성자</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->user?->nick_name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">내용</span>
                    <div class="board-field board-content bg-light rounded-3 px-3 py-2">
                        <div class="board-content-text">{{ $post->content }}</div>
                    </div>
                </div>
                <div class="board-meta d-flex flex-wrap gap-2 text-secondary small align-items-baseline">
                    <span>등록시각: {{ $post->create_datetime }}</span>
                    @if (!empty($post->update_datetime))
                        <span class="text-danger">(수정시각: {{ $post->update_datetime }})</span>
                    @endif
                    <span class="ms-auto text-nowrap">공개여부:</span>
                    <span class="badge use-flag use-flag-{{ $post->use_flag ?? 0 }}">{{ config("const.use_flag.{$useFlag}", '-') }}</span>
                </div>
            </div>

        </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex gap-2 ms-auto">
                @can('updateUseFlag', $post)
                    <button type="button" id="btn_post_modify" class="btn btn-outline-secondary">수정</button>
                    <button type="button" id="btn_post_delete" class="btn btn-outline-danger {{ (($post->use_flag ?? 0) == 1) ? 'd-none' : '' }}">삭제</button>
                    <button type="button" id="btn_post_use_flag" class="btn btn-outline-primary">공개설정</button>
                @endcan
                <button type="button" id="btn_post_list" class="btn btn-secondary">목록</button>
            </div>
        </div>

    </section>
@endsection

@section('script')
    <script>
        $(function(){

            const listUrl = "{{ route('admins.posts.index', ['post_type' => 'notice']) }}";
            const editUrl = "{{ route('admins.posts.edit', ['post_type' => 'notice', 'idx' => $post->idx]) }}";
            const deleteUrl = "{{ route('admins.posts.soft.delete', ['post_type' => 'notice', 'idx' => $post->idx]) }}";
            const useFlagUrl = "{{ route('admins.posts.use_flag.update', ['post_type' => 'notice', 'idx' => $post->idx]) }}";
            const useFlagLabelMap = @json(config('const.use_flag', []));
            let currentUseFlag = Number("{{ $post->use_flag ?? 0 }}");
            const $useFlagBadge = $('.use-flag');
            const $modifyBtn = $('#btn_post_modify');
            const $deleteBtn = $('#btn_post_delete');

            function normalizeUseFlag(flag) {
                if (flag === 1 || flag === '1' || flag === true) {
                    return 1;
                }
                if (typeof flag === 'string') {
                    const upper = flag.trim().toUpperCase();
                    if (upper === 'Y' || upper === 'YES' || upper === 'TRUE') {
                        return 1;
                    }
                }
                return 0;
            }

            function getUseFlagLabel(flag) {
                return Number(flag) === 1
                    ? (useFlagLabelMap.Y ?? '공개')
                    : (useFlagLabelMap.N ?? '비공개');
            }

            function syncUseFlagUi(flag) {
                const normalized = normalizeUseFlag(flag);

                $useFlagBadge
                    .removeClass('use-flag-0 use-flag-1')
                    .addClass(`use-flag-${normalized}`)
                    .text(getUseFlagLabel(normalized));

                $deleteBtn.toggleClass('d-none', normalized === 1);
            }

            currentUseFlag = normalizeUseFlag(currentUseFlag);
            syncUseFlagUi(currentUseFlag);

            $("#btn_post_list").on("click", function() {
                location.href = listUrl;
            });

            $("#btn_post_modify").on("click", function() {
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
                        alert('공지사항이 삭제되었습니다.');
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

            $('#btn_post_use_flag').on('click', function(){
                const message = currentUseFlag === 1
                    ? '이미 공개중입니다. 비공개로 하시겠습니까?'
                    : '현재 비공개입니다. 공개로 하시겠습니까?';

                if (!confirm(message)) {
                    return;
                }

                requestAjax({
                    method: 'PATCH',
                    url: useFlagUrl,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    onSuccess: function (data) {
                        const nextUseFlag = normalizeUseFlag(data && data.use_flag !== undefined
                            ? data.use_flag
                            : (currentUseFlag === 1 ? 0 : 1));
                        currentUseFlag = nextUseFlag;
                        syncUseFlagUi(nextUseFlag);
                        alert('공개 여부가 변경되었습니다.');
                    },
                    onError: function (xhr) {
                        let message = '공개여부 변경 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
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
