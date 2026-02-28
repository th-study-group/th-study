@extends('layouts.app')

@section('title', $metaTitle ?? '상세내역')
@section('meta_description', $metaDescription ?? '')
@section('og_title', $metaTitle ?? '')
@section('og_description', $metaDescription ?? '')
@section('og_image', $metaImage ?? asset('images/og/001.png'))

@push('styles')
  <link href="{{ asset('css/blog.css') }}?v={{ filemtime(public_path('css/blog.css')) }}" rel="stylesheet" />
@endpush

@section('content')
  <section class="col-12 col-lg-8 mx-auto blog-page-scope">
    <div class="board-card blog-show-page p-3 p-lg-4 rounded-3 shadow-sm">
      <button type="button" class="btn_note_list btn btn-dark btn-sm blog-show-top-list">목록</button>
      <h1 class="blog-show-title">{{ $note->subject }}</h1>

      <div class="blog-show-meta">
        <span class="blog-show-meta-topic">{{ $note->group_topic_name }}</span>
        <span class="blog-show-meta-date">{{ $note->create_datetime?->format('Y-m-d H:i:s') ?? '-' }}</span>
      </div>

      @if (auth()->check() && auth()->user()?->level === 'admin')
        <div class="blog-show-visibility">
          <span class="blog-show-visibility-badge {{ $useFlag === 'Y' ? 'is-public' : '' }}">{{ config("const.use_flag.{$useFlag}", '-') }}</span>
        </div>
      @endif

      <article class="blog-show-content">{!! $contentHtml !!}</article>

      @if (($note->tags ?? collect())->isNotEmpty())
        <ul class="blog-show-tags">
          @foreach ($note->tags as $tag)
            <li>#{{ $tag->name }}</li>
          @endforeach
        </ul>
      @endif

      <div class="blog-show-actions">
        @can('update', $note)
          <button type="button" id="btn_note_modify" class="btn btn-outline-secondary">수정</button>
        @endcan
        @if (($note->use_flag ?? 'N') !== 'Y')
          @can('delete', $note)
            <button type="button" id="btn_note_delete" class="btn btn-outline-danger">삭제</button>
          @endcan
        @endif
        @can('updateUseFlag', $note)
          <button type="button" id="btn_note_use_flag" class="btn btn-outline-primary">공개설정</button>
        @endcan
        <button type="button" class="btn_note_list btn btn-dark">목록</button>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/blog.js') }}?v={{ filemtime(public_path('js/blog.js')) }}" defer></script>  
@endpush

@section('script')
  <script>
    $(function() {
      const listUrl = "{{ route("{$group}.index", ['slug' => $slug]) }}";
      const editUrl = "{{ route("{$group}.edit", ['slug' => $slug, 'idx' => $note->idx]) }}";
      const deleteUrl = "{{ route("{$group}.soft.delete", ['slug' => $slug, 'idx' => $note->idx]) }}";
      const useFlagUrl = "{{ route("{$group}.use_flag.update", ['slug' => $slug, 'idx' => $note->idx]) }}";
      const useFlag = "{{ $note->use_flag ?? 'N' }}";

      $(".btn_note_list").on("click", function() {
        location.href = listUrl;
      });

      $("#btn_note_modify").on("click", function() {
        if (!confirm('수정하시겠습니까?')) {
          return;
        }
        location.href = editUrl;
      });

      $('#btn_note_delete').on('click', function(){
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
                alert('노트가 삭제되었습니다.');
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

      $('#btn_note_use_flag').on('click', function(){
        const message = useFlag === 'Y'
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
          onSuccess: function () {
              alert('공개 여부가 변경되었습니다.');
              location.reload();
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
