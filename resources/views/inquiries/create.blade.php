@extends('layouts.app')

@section('title', '등록')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">문의하기</h2>
                    <p class="text-secondary small mb-0">문의 내용을 작성해 주세요.</p>
                </div>
            </div>

            <form class="board-form mt-3" method="post" action="{{ route('inquiries.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="inquiry-title" class="form-label small text-secondary">제목</label>
                    <input type="text" id="inquiry-title" name="title" class="form-control" placeholder="제목을 입력해 주세요">
                </div>
                <div class="mb-3">
                    <label for="inquiry-content" class="form-label small text-secondary">내용</label>
                    <textarea id="inquiry-content" name="content" class="form-control board-textarea" rows="8" placeholder="문의 내용을 입력해 주세요"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('inquiries.index') }}" class="btn btn-outline-secondary">취소</a>
                    <button type="submit" class="btn btn-primary">등록</button>
                </div>
            </form>
        </div>
    </section>
@endsection
