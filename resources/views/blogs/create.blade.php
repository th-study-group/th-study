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
        @if ($isEditMode)
          @method('PUT')
        @endif

        <div class="mb-3">
          <label for="subject" class="form-label blog-create-label">제목</label>
          <input type="text" 
                 id="subject" 
                 name="subject" 
                 class="form-control blog-create-input @error('subject') is-invalid @enderror"
                 value="{{ old('subject', $note->subject ?? '') }}"
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
              <option value="{{ $topic->idx }}" {{ old('topic', $note->topic_idx ?? null) == $topic->idx ? 'selected' : '' }}>
                {{ $topic->name }}
              </option>
            @endforeach
          </select>
          @error('topic')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        @if ($isEditMode)
          <div class="mb-3">
            <span class="form-label d-block blog-create-label">공개여부</span>
            <div class="blog-create-radio-wrap">
              <div class="form-check mb-0">
                <input class="form-check-input" type="radio" id="use_flag_y" name="usg_flag" value="Y" {{ old('usg_flag', $note->use_flag ?? 'N') === 'Y' ? 'checked' : '' }}>
                <label class="form-check-label" for="use_flag_y">공개</label>
              </div>
              <div class="form-check mb-0">
                <input class="form-check-input" type="radio" id="use_flag_n" name="usg_flag" value="N" {{ old('usg_flag', $note->use_flag ?? 'N') === 'N' ? 'checked' : '' }}>
                <label class="form-check-label" for="use_flag_n">비공개</label>
              </div>
            </div>
            @error('usg_flag')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>
        @endif

        <div class="mb-3">
          <label for="content" class="form-label blog-create-label">내용</label>
          <textarea id="content" name="content" class="d-none" rows="16" placeholder="내용을 입력하세요.">{{ old('content', $note->content ?? '') }}</textarea>
          <div id="blogContentEditor" class="js-toast-ui-editor" data-source-selector="#content"></div>
          @error('content')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="thumbnail_path" class="form-label blog-create-label">대표이미지</label>
          <div id="thumbnail_path_picker" class="thumbnail_path-picker" @if ($hasSavedThumbnail) style="display:none" @endif>
            <input type="file" id="thumbnail_path" name="thumbnail_path" class="form-control blog-create-thumbnail-input @error('thumbnail_path') is-invalid @enderror" accept="image/*">
            <button type="button" id="thumbnail_path_trigger" class="blog-create-thumbnail-trigger">파일 선택</button>
            <span id="thumbnail_path_name" class="blog-create-thumbnail-name">선택된 파일 없음</span>
          </div>
          @error('thumbnail_path')
            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
          @enderror
          <div id="blogThumbnailFileInfo" class="blog-create-thumbnail-fileinfo" @if (! $hasSavedThumbnail) style="display:none" @endif>
            <span id="blogThumbnailFileName" class="blog-create-thumbnail-filename">{{ $savedThumbnailName }}</span>
            <div class="blog-create-thumbnail-actions">
              <a id="blogThumbnailViewBtn" class="blog-create-thumbnail-action-btn is-view" href="{{ $savedThumbnailUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" @if (! $hasSavedThumbnail) style="display:none" @endif>보기</a>
              <button type="button" id="blogThumbnailRemoveBtn" class="blog-create-thumbnail-action-btn is-remove" aria-label="첨부 파일 삭제">삭제</button>
            </div>
          </div>
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
          <input type="hidden" id="tags" name="tags" value="{{ $initialTagsValue }}">
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
      const isEditMode = {{ $isEditMode ? 'true' : 'false' }};
      const thumbnailDestroyUrl = "{{ $thumbnailDestroyUrl ?? '' }}";
      const tagsDestroyUrl = "{{ $tagsDestroyUrl ?? '' }}";
      const tagManager = createTagManager({
        chipsSelector: '#blogTagChips',
        hiddenSelector: '#tags',
        maxCount: 10,
      });
      const initialTags = ($('#tags').val() || '')
        .split(',')
        .map(function (tag) { return String(tag).trim(); })
        .filter(function (tag) { return tag !== ''; });
      let isComposingTag = false;

      initialTags.forEach(function (tag) {
        tagManager.addTag(tag);
      });

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
        const tag = String($(this).data('tag') ?? '').trim();

        if (!tag) {
          return;
        }

        if (!isEditMode) {
          tagManager.removeTag(tag);
          return;
        }

        requestAjax({
          method: 'DELETE',
          url: tagsDestroyUrl,
          dataType: 'json',
          data: { tag: tag },
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          onSuccess: function () {
            tagManager.removeTag(tag);
          },
          onError: function () {
            alert('해시태그 삭제 중 오류가 발생했습니다.');
          },
        });
      });

      $('#blogThumbnailRemoveBtn').on('click', function () {
        if (!isEditMode) {
          $('#thumbnail_path').val('');
          updateThumbnailName(document.getElementById('thumbnail_path'), '#thumbnail_path_name');
          return;
        }

        if (!confirm('기존 썸네일을 삭제하시겠습니까?')) {
          return;
        }

        requestAjax({
          method: 'PATCH',
          url: thumbnailDestroyUrl,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          onSuccess: function () {
            $('#blogThumbnailFileInfo').hide();
            $('#blogThumbnailViewBtn').hide().attr('href', '#');
            $('#blogThumbnailFileName').text('');
            $('#thumbnail_path_picker').show();
            $('#thumbnail_path').val('');
            updateThumbnailName(document.getElementById('thumbnail_path'), '#thumbnail_path_name');
          },
          onError: function () {
            alert('썸네일 삭제 중 오류가 발생했습니다.');
          },
        });
      });

      tagManager.render();
    });
  </script>
@endsection
