<div class="comment-section">
    @php($openCommentEditIdx = session('open_comment_edit_idx'))
    @php($postTypeExcluded = config('board.post_type_excluded', []))
    @php($isCommentActionHidden = in_array($post->post_type, $postTypeExcluded, true))
    <h3 class="h6 mb-3">댓글</h3>

    @can('create', [\App\Models\Comment::class, $post])
        <form id="form_register_commtent" name="form_register_commtent" class="comment-form" method="POST" action="{{ route('comments.store') }}">
            @csrf
            <input type="hidden" name="post_idx" value="{{ $post->idx }}">
            <div class="mb-2">
                <textarea id="content" name="content" class="form-control board-textarea @error('content') is-invalid @enderror" rows="3" placeholder="댓글을 입력해 주세요">{{ $openCommentEditIdx ? '' : old('content') }}</textarea>
                @error('content')
                <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" id="btn_comment_register" class="btn btn-primary btn-sm">댓글 등록</button>
            </div>
        </form>
    @endcan

    <div class="comment-list mt-4">
        @if (!empty($comments) && $comments->count() > 0)
            @foreach ($comments as $comment)
                <div class="comment-item border rounded-3 p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <strong class="comment-author">{{ $comment->user?->name ?? '-' }}</strong>
                            <span class="text-secondary small ms-2">
                                {{ $comment->create_datetime?->diffForHumans() ?? '-' }}
                            </span>
                        </div>
                        @if (!$isCommentActionHidden)
                            <div class="comment-actions d-flex gap-1">
                                @can('update', $comment)
                                    <button
                                        type="button"
                                        class="btn btn_comment_edit_choice btn-outline-secondary btn-sm"
                                        data-comment-idx="{{ $comment->idx }}">
                                        수정
                                    </button>
                                @endcan
                                @can('delete', $comment)
                                    <form class="form_delete_comment d-inline" method="POST" action="{{ route('comments.soft.delete', ['idx' => $comment->idx]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn_comment_delete btn-outline-danger btn-sm">삭제</button>
                                    </form>
                                @endcan
                            </div>
                        @endif
                    </div>
                    <p class="comment-content-text mb-0 mt-2">{{ $comment->content }}</p>
                    <textarea class="comment-origin-content d-none">{{ $comment->content }}</textarea>

                    @if (!$isCommentActionHidden)
                        @can('update', $comment)
                            <div class="comment_edit_inline border-top pt-3 mt-3 {{ $openCommentEditIdx === $comment->idx ? '' : 'd-none' }}">
                                <h4 class="h6 mb-2">댓글 수정</h4>
                                <form class="form_update_comment comment-form" method="POST" action="{{ route('comments.update', ['idx' => $comment->idx]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-2">
                                        <textarea
                                            class="edit_content form-control board-textarea {{ $openCommentEditIdx === $comment->idx && $errors->commentUpdate->has('content') ? 'is-invalid' : '' }}"
                                            name="content"
                                            rows="3"
                                            placeholder="수정할 내용을 입력해 주세요">{{ $openCommentEditIdx === $comment->idx ? old('content') : '' }}</textarea>
                                        @if ($openCommentEditIdx === $comment->idx)
                                            @error('content', 'commentUpdate')
                                                <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn_comment_edit_cancel btn-outline-secondary btn-sm">취소</button>
                                        <button type="button" class="btn btn_comment_modify btn-primary btn-sm">수정 적용</button>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    @endif
                </div>
            @endforeach
        @else
        <div class="comment-empty border rounded-3 p-3 text-center text-secondary small">
            등록된 댓글이 없습니다.
        </div>
        @endif
    </div>

    @if (!empty($comments) && $comments->hasPages())
        <nav class="board-pagination d-flex justify-content-center mt-3" aria-label="댓글 페이지네이션">
            {{ $comments->links('pagination.simple') }}
        </nav>
    @endif
</div>

@push('scripts')
    <script>
        $(function() {
            $("#btn_comment_register").on("click", function() {
                const content = $.trim($("#content").val());
                if (content === "") {
                    alert("댓글 내용을 입력해 주세요.");
                    $("#content").focus();
                    return;
                }

                if (!confirm("댓글을 등록하시겠습니까?")) {
                    return;
                }
                
                $("#form_register_commtent").submit();
            });

            $(document).on("click", ".btn_comment_modify", function() {
                const $form = $(this).closest(".form_update_comment");
                const content = $.trim($form.find(".edit_content").val());
                if (content === "") {
                    alert("수정할 댓글 내용을 입력해 주세요.");
                    $form.find(".edit_content").focus();
                    return;
                }

                if (!confirm('정말로 수정하시겠습니까?')) {
                    return;
                }

                $form.submit();
            });

            $(document).on("click", ".btn_comment_delete", function() {
                if (!confirm('정말로 삭제 하시겠습니까?')) {
                    return;
                }

                $(this).closest(".form_delete_comment").submit();
            });

            $(document).on("click", ".btn_comment_edit_choice", function() {
                const $commentItem = $(this).closest(".comment-item");
                const commentContent = $commentItem.find(".comment-origin-content").val();
                const $targetEditWrap = $commentItem.find(".comment_edit_inline");

                $(".comment_edit_inline").addClass("d-none");
                $(".edit_content").val("");

                $targetEditWrap.find(".edit_content").val(commentContent);
                $targetEditWrap.removeClass("d-none");
            });

            $(document).on("click", ".btn_comment_edit_cancel", function() {
                const $commentItem = $(this).closest(".comment-item");
                const $targetEditWrap = $commentItem.find(".comment_edit_inline");

                $targetEditWrap.find(".edit_content").val("");
                $targetEditWrap.addClass("d-none");
            });
        });
    </script>
@endpush
