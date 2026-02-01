<div class="comment-section">
    <h3 class="h6 mb-3">댓글</h3>

    {{-- 댓글 적용 시 권한 관리 필수 --}}
    {{--
    <form id="form_register_commtent" name="form_register_commtent" class="comment-form" method="POST" action="{{ route('comments.store') }}">
        @csrf
        <div class="mb-2">
            <textarea id="content" name="content" class="form-control board-textarea" rows="3" placeholder="댓글을 입력해 주세요"></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" id="btn_comment_register" class="btn btn-primary btn-sm">댓글 등록</button>
        </div>
    </form>
    --}}

    <div class="comment-list mt-4">

        {{-- 등록된거 보여줄 때 --}}
        {{--
        <div class="comment-item border rounded-3 p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <strong class="comment-author">홍길동</strong>
                    <span class="text-secondary small ms-2">2026-01-29 11:20</span>
                </div>
                <div class="comment-actions d-flex gap-1">
                    <button type="button" class="btn btn_comment_modify btn-outline-secondary btn-sm">수정</button>
                    <button type="button" class="btn btn_comment_delete btn-outline-danger btn-sm">삭제</button>
                </div>
            </div>
            <p class="mb-0 mt-2">댓글 더미 텍스트입니다. 댓글 더미 텍스트입니다.</p>
        </div>
        --}}

        <div class="comment-empty border rounded-3 p-3 text-center text-secondary small">
            등록된 댓글이 없습니다.
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function(){

           $("#btn_comment_register").on("click", function() {
                alert("ok!!");
           });
        });
    </script>
@endpush
