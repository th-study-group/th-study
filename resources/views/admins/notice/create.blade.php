@extends('layouts.app')

@section('title', '작성')

@section('content')
    @php($isEdit = ($mode ?? '') === 'edit')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0">공지사항 작성할 수 있습니다.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-warning d-flex align-items-center gap-2 small mt-3" role="alert">
                    <span class="badge text-bg-warning text-dark">경고</span>
                    <span>공지사항 등록 실패 사유를 확인하세요.</span>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success mt-3">{{ session('status') }}</div>
            @endif

            <form id="form-notice" class="board-form mt-3" method="post" action="{{ $isEdit ? route('admins.posts.update', ['post_type' => 'notice', 'idx' => $post->idx]) : route('admins.posts.store', ['post_type' => 'notice']) }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="title" class="form-label small text-secondary">제목</label>
                    <input type="text" 
                           id="title" 
                           name="title"
                           value="{{ old('title', $post->title ?? '') }}" 
                           class="form-control @error('title') is-invalid @enderror" 
                           placeholder="제목을 입력해 주세요">
                    @error('title')
                        <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label small text-secondary">내용</label>
                    <textarea id="content" name="content" class="form-control board-textarea @error('content') is-invalid @enderror" rows="8" placeholder="공지사항 내용을 입력해 주세요">{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="btn_save" class="btn btn-primary">{{ $isEdit ? '수정' : '적용' }}</button>
                    <a href="{{ route('admins.posts.index', ['post_type' => 'notice']) }}" class="btn btn-outline-secondary">목록</a>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            $("#btn_save").on("click", function(){ 
                if ($.trim($('#title').val()) === '') {
                    alert('제목을 입력해 주세요.');
                    $('#title').focus();
                    return;
                }

                if ($.trim($('#content').val()) === '') {
                    alert('내용을 입력해 주세요.');
                    $('#content').focus();
                    return;
                }

                if (!confirm('적용하시겠습니까')) { 
                    return;
                }
                
                $("#form-notice").submit();
            });
        });
    </script>
@endsection
