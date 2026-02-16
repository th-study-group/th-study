@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0">게시물의 상세정보를 확인하세요</p>
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
                <div class="board-meta text-secondary small">
                    등록시각: {{ $post->create_datetime }}
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('posts.index', ['post_type' => 'notice']) }}" class="btn btn-secondary">목록</a>
        </div>
    </section>
@endsection
