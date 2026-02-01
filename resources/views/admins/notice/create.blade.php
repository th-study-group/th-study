@extends('layouts.app')

@section('title', '작성')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0">공지사항 작성할 수 있습니다.</p>
                </div>
            </div>

            <form class="board-form mt-3" method="post" action="{{ route('admins.posts.create', ['post_type' => 'notice']) }}">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label small text-secondary">제목</label>
                    <input type="text" 
                           id="title" 
                           name="title"
                           value="" 
                           class="form-control" 
                           placeholder="제목을 입력해 주세요">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label small text-secondary">내용</label>
                    <textarea id="content" name="content" class="form-control board-textarea" rows="8" placeholder="문의 내용을 입력해 주세요"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="btn_save" class="btn btn-primary">적용</button>
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
                if (!confirm('적용하시겠습니까')) { 
                    return;
                }
                alert('ok!!');
            });
        });
    </script>
@endsection