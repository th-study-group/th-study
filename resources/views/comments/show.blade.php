<div class="comment-section">
    <h3 class="h6 mb-3">댓글</h3>

    <div class="comment-list mt-4">
        @if (!empty($comments) && $comments->isNotEmpty())
            @foreach ($comments as $comment)
                <div class="comment-item border rounded-3 p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <strong class="comment-author">{{ $comment->user?->name ?? '-' }}</strong>
                            <span class="text-secondary small ms-2">
                                {{ $comment->create_datetime?->diffForHumans() ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <p class="mb-0 mt-2">{{ $comment->content }}</p>
                </div>
            @endforeach
        @else
            <div class="comment-empty border rounded-3 p-3 text-center text-secondary small">
                등록된 댓글이 없습니다.
            </div>
        @endif
    </div>
</div>
