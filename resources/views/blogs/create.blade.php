@extends('layouts.app')

@section('title', '작성')

@push('styles')
  <link href="{{ asset('css/blog.css') }}?v={{ filemtime(public_path('css/blog.css')) }}" rel="stylesheet" />
@endpush

@section('content')
  <section class="col-12 col-lg-8 mx-auto">
    <div class="board-card blog-create-page rounded-3 p-3 p-lg-4 shadow-sm">
      <h1 class="blog-create-title">노트 작성</h1>
      <p class="blog-create-description">블로그 글을 작성할 수 있습니다.</p>

      @if ($errors->any())
        <div class="alert alert-warning d-flex align-items-center gap-2 small mt-3" role="alert">
          <span class="badge text-bg-warning text-dark">경고</span>
          <span>노트 등록 실패 사유를 확인하세요.</span>
        </div>
      @endif

      @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif

      <form id="form-note" method="post" action="{{ $formAction }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label for="subject" class="form-label blog-create-label">제목</label>
          <input type="text" 
                 id="subject" 
                 name="subject" 
                 class="form-control blog-create-input @error('subject') is-invalid @enderror"
                 value="{{ old('subject') }}"
                 maxlength="255" 
                 placeholder="제목을 입력하세요.">
          @error('subject')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="topic" class="form-label blog-create-label">주제</label>
          <select id="topic" name="topic" class="form-select blog-create-select @error('topic') is-invalid @enderror">
            <option value="">주제를 선택해 주세요.</option>
            @foreach (($topics ?? collect()) as $topic)
              <option value="{{ $topic->idx }}" {{ old('topic') == $topic->idx ? 'selected' : '' }}>
                {{ $topic->name }}
              </option>
            @endforeach
          </select>
          @error('topic')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        {{--
        <div class="mb-3">
          <span class="form-label d-block blog-create-label">공개여부</span>
          <div class="blog-create-radio-wrap">
            <div class="form-check mb-0">
              <input class="form-check-input" type="radio" id="use_flag" name="usg_flag" value="Y" checked>
              <label class="form-check-label" for="use_flag">공개</label>
            </div>
            <div class="form-check mb-0">
              <input class="form-check-input" type="radio" name="usg_flag" id="use_flag" value="N">
              <label class="form-check-label" for="usg_flag">비공개</label>
            </div>
          </div>
        </div>
        --}}

        <div class="mb-3">
          <label for="content" class="form-label blog-create-label">내용</label>
          <textarea id="content" name="content" class="d-none" rows="16" placeholder="내용을 입력하세요.">{{ old('content') }}</textarea>
          <div id="blogContentEditor" class="js-toast-ui-editor" data-source-selector="#content"></div>
          @error('content')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="thumbnail_path" class="form-label blog-create-label">대표이미지</label>
          {{-- 수정모드에서 DB에 이미 저장된 대표이미지가 있을 때만 아래 파일 선택 영역을 숨기면 됩니다.
               예: @if($isEditMode && !empty($savedThumbnailPath)) style="display:none" @endif --}}
          <div id="thumbnail_path_picker" class="thumbnail_path-picker">
            <input type="file" id="thumbnail_path" name="thumbnail_path" class="form-control blog-create-thumbnail-input @error('thumbnail_path') is-invalid @enderror" accept="image/*">
            <button type="button" id="thumbnail_path_trigger" class="blog-create-thumbnail-trigger">파일 선택</button>
            <span id="thumbnail_path_name" class="blog-create-thumbnail-name">선택된 파일 없음</span>
          </div>
          @error('thumbnail_path')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
          @enderror
          {{-- 파일 첨부 후 표시되는 "파일명 + 보기/삭제" UI는 현재 단계에서 숨김 처리.
               수정모드에서 DB 저장 파일 관리가 필요해질 때 아래 블록 주석 해제해서 사용하세요. --}}
          {{--
            <div id="blogThumbnailFileInfo" class="blog-create-thumbnail-fileinfo">
              <span id="blogThumbnailFileName" class="blog-create-thumbnail-filename"></span>
              <div class="blog-create-thumbnail-actions">
                <button type="button" id="blogThumbnailViewBtn" class="blog-create-thumbnail-action-btn is-view" aria-label="이미지 보기">
                  <svg class="blog-create-thumbnail-eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  보기
                </button>
                <button type="button" id="blogThumbnailRemoveBtn" class="blog-create-thumbnail-action-btn is-remove" aria-label="첨부 파일 삭제">삭제</button>
              </div>
            </div>
          --}}
        </div>

        <div>
          <span class="form-label d-block blog-create-label">해시태그</span>
          <div class="blog-create-tags-wrap">
            <div class="blog-create-tag-editor">
              <ul id="blogTagChips" class="blog-create-tag-chips"></ul>
              <input type="text" 
                     id="blogTagInput" 
                     class="blog-create-tag-input" 
                     maxlength="20" 
                     placeholder="#태그입력">
            </div>
          </div>
          <input type="hidden" id="tags" name="tags">
          @error('tags')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="blog-create-footer">
          <button type="button" id="btn_save" class="btn btn-primary">적용</button>
          <a href="{{ route("{$group}.index", ['slug' => $slug]) }}" class="btn btn-outline-secondary">
            목록
          </a>
        </div>
      </form>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/toast_ui_editor.js') }}?v={{ filemtime(public_path('js/toast_ui_editor.js')) }}" defer></script>
  <script src="{{ asset('js/blog.js') }}?v={{ filemtime(public_path('js/blog.js')) }}" defer></script>  
@endpush

@section('script')
  <script>
    $(function () {
      const tagManager = createTagManager({
        chipsSelector: '#blogTagChips',
        hiddenSelector: '#tags',
        maxCount: 10,
      });
      let isComposingTag = false;

      $("#btn_save").on("click", function() {
        if (confirm("적용하시겠습니까?") == false) {
          return;
        }

        const subject = $('#subject').val().trim();
        const topic = $('#topic').val();
        const content = $('#content').val().trim();

        if (!subject) {
          alert('제목을 입력해 주세요.');
          $('#subject').trigger('focus');
          return;
        }

        if (!topic) {
          alert('주제를 선택해 주세요.');
          $('#topic').trigger('focus');
          return;
        }

        if (!content) {
          alert('내용을 입력해 주세요.');

          const $editorInput = $('#blogContentEditor').find('.toastui-editor-ww-container .ProseMirror');
          if ($editorInput.length > 0) {
            $editorInput.trigger('focus');
          } else {
            $('#content').trigger('focus');
          }

          return;
        }
        
        $("#form-note").submit();
      });

      $('#thumbnail_path_trigger').on('click', function () {
        $('#thumbnail_path').trigger('click');
      });

      $('#thumbnail_path').on('change', function () {
        updateThumbnailName(this, '#thumbnail_path_name');
      });

      $('#blogTagInput').on('compositionstart', function () {
        isComposingTag = true;
      });

      $('#blogTagInput').on('compositionend', function () {
        isComposingTag = false;
      });

      $('#blogTagInput').on('keydown', function (e) {
        if (isComposingTag || e.isComposing) {
          return;
        }

        if (e.key === 'Enter' || e.key === ',') {
          e.preventDefault();
          
          const result = tagManager.addTag($(this).val());
          
          if (!result.ok && result.reason === 'max') {
            alert('해시태그는 최대 10개까지 등록할 수 있습니다.');
          }
          $(this).val('');
          
        }
      });

      $('#blogTagChips').on('click', '.js-tag-remove', function () {
        tagManager.removeTag($(this).data('tag'));
      });

      tagManager.render();
    });
  </script>
@endsection
