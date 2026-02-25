@extends('layouts.app')

@section('title', '작성')

@section('style')
  <style>
    .blog-create-page {
      background: #fff;
      border: 1px solid #e9ecef;
    }

    .blog-create-title {
      margin: 0 0 4px;
      font-weight: 700;
      color: #212529;
      font-size: 20px;
      letter-spacing: -0.02em;
    }

    .blog-create-description {
      color: #6c757d;
      font-size: 14px;
      margin: 0 0 14px;
    }

    .blog-create-label {
      font-weight: 500;
      color: #6c757d;
      margin-bottom: 8px;
      font-size: 13px;
    }

    .blog-create-input,
    .blog-create-select {
      height: 46px;
      border: 1px solid #c9cfd8;
      border-radius: 8px;
      background: #fff;
    }

    .blog-create-select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      padding-right: 40px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 12px center;
      background-size: 14px;
    }

    .blog-create-radio-wrap {
      display: flex;
      align-items: center;
      gap: 14px;
      min-height: 46px;
      padding: 0 2px;
    }

    .blog-create-tags-wrap {
      border: 1px solid #d5dbe4;
      border-radius: 8px;
      background: #fff;
      padding: 12px;
    }

    .blog-create-tag-editor {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      min-height: 52px;
      border: 1px solid #c9cfd8;
      border-radius: 8px;
      padding: 8px 10px;
      background: #fff;
    }

    .blog-create-tag-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 0;
      padding: 0;
      list-style: none;
      align-items: center;
    }

    .blog-create-tag-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #d4dbe6;
      border-radius: 999px;
      padding: 6px 10px;
      background: #f8fafc;
    }

    .blog-create-tag-text {
      color: #1f2937;
      font-size: 14px;
      font-weight: 600;
    }

    .blog-create-tag-remove {
      border: 0;
      background: transparent;
      color: #9ca3af;
      font-size: 18px;
      line-height: 1;
      padding: 0;
      width: 16px;
      height: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .blog-create-tag-input {
      flex: 1 1 180px;
      border: 0;
      outline: none;
      min-width: 160px;
      height: 32px;
      font-size: 16px;
      color: #374151;
      padding: 0;
      background: transparent;
    }

    .blog-create-footer {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 18px;
    }

    .blog-create-thumbnail-picker {
      display: block;
    }

    .blog-create-thumbnail-input {
      height: 52px;
      border: 1px solid #c9cfd8;
      border-radius: 8px;
      background: #fff;
      padding: 0;
      line-height: 52px;
      overflow: hidden;
    }

    .blog-create-thumbnail-input::file-selector-button {
      height: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 168px;
      margin-right: 12px;
      border: 0;
      border-right: 1px solid #c9cfd8;
      background: #f3f4f6;
      padding: 0;
      color: #212529;
      font-weight: 600;
      line-height: 1;
      text-align: center;
      cursor: pointer;
    }

    .blog-create-thumbnail-input::-webkit-file-upload-button {
      height: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 168px;
      margin-right: 12px;
      border: 0;
      border-right: 1px solid #c9cfd8;
      background: #f3f4f6;
      padding: 0;
      color: #212529;
      font-weight: 600;
      line-height: 1;
      text-align: center;
      cursor: pointer;
    }

    .blog-create-thumbnail-fileinfo {
      display: none;
      margin-top: 10px;
      border: 1px solid #d9dee7;
      border-radius: 10px;
      padding: 8px 10px;
      background: #f8fafc;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .blog-create-thumbnail-filename {
      color: #374151;
      font-size: 14px;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      min-width: 0;
      flex: 1;
    }

    .blog-create-thumbnail-actions {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0;
    }

    .blog-create-thumbnail-action-btn {
      height: 32px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      background: #fff;
      color: #6b7280;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      font-size: 13px;
      font-weight: 600;
      line-height: 1;
      padding: 0 10px;
      cursor: pointer;
    }

    .blog-create-thumbnail-action-btn.is-remove {
      color: #b91c1c;
      border-color: #efb4b4;
      background: #fff5f5;
    }

    .blog-create-thumbnail-action-btn.is-view {
      display: none;
    }

    .blog-create-thumbnail-eye-icon {
      width: 14px;
      height: 14px;
      display: block;
    }

    @media (max-width: 991px) {
      .blog-create-title {
        font-size: 22px;
      }

      .blog-create-tag-input {
        min-width: 120px;
        flex-basis: 120px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $slug = (string) request()->route('slug', 'develop');
    $listUrl = route('blogs.index', ['slug' => $slug]);
    $topics = [
      '국내여행',
      '국내맛집',
      '초딩도 쉽게하는 라라벨',
      '초딩도 쉽게 이해하는 경제',
      '라라벨 기초',
    ];
  @endphp

  <section class="col-12 col-lg-8 mx-auto">
    <div class="board-card blog-create-page rounded-3 p-3 p-lg-4 shadow-sm">
      <h1 class="blog-create-title">노트 작성</h1>
      <p class="blog-create-description">블로그 글을 작성할 수 있습니다.</p>

      <form id="blogCreateForm" method="post" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label for="blogTitle" class="form-label blog-create-label">제목</label>
          <input type="text" id="blogTitle" name="title" class="form-control blog-create-input" maxlength="255" placeholder="제목을 입력하세요.">
        </div>

        <div class="mb-3">
          <label for="blogTopic" class="form-label blog-create-label">주제</label>
          <select id="blogTopic" name="topic" class="form-select blog-create-select">
            <option value="전체" selected>전체</option>
            @foreach ($topics as $topic)
              <option value="{{ $topic }}">{{ $topic }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <span class="form-label d-block blog-create-label">공개여부</span>
          <div class="blog-create-radio-wrap">
            <div class="form-check mb-0">
              <input class="form-check-input" type="radio" name="is_public" id="isPublicY" value="1" checked>
              <label class="form-check-label" for="isPublicY">공개</label>
            </div>
            <div class="form-check mb-0">
              <input class="form-check-input" type="radio" name="is_public" id="isPublicN" value="0">
              <label class="form-check-label" for="isPublicN">비공개</label>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="blogContent" class="form-label blog-create-label">내용</label>
          <textarea id="blogContent" name="content" class="d-none" rows="16" placeholder="내용을 입력하세요."></textarea>
          <div id="blogContentEditor" class="js-toast-ui-editor" data-source-selector="#blogContent"></div>
        </div>

        <div class="mb-3">
          <label for="blogThumbnail" class="form-label blog-create-label">대표이미지</label>
          {{-- 수정모드에서 DB에 이미 저장된 대표이미지가 있을 때만 아래 파일 선택 영역을 숨기면 됩니다.
               예: @if($isEditMode && !empty($savedThumbnailPath)) style="display:none" @endif --}}
          <div id="blogThumbnailPicker" class="blog-create-thumbnail-picker">
            <input type="file" id="blogThumbnail" name="thumbnail" class="form-control blog-create-thumbnail-input" accept="image/*">
          </div>
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
              <input type="text" id="blogTagInput" class="blog-create-tag-input" maxlength="30" placeholder="#태그입력">
            </div>
          </div>
          <input type="hidden" id="blogTagsHidden" name="tags">
        </div>

        <div class="blog-create-footer">
          <button type="button" class="btn btn-primary">적용</button>
          <a href="{{ $listUrl }}" class="btn btn-outline-secondary">목록</a>
        </div>
      </form>
    </div>
  </section>
@endsection

@push('scripts')
  <script src="{{ asset('js/toast_ui_editor.js') }}?v={{ filemtime(public_path('js/toast_ui_editor.js')) }}" defer></script> 
@endpush

@section('script')
  <script>
    $(function () {
      var tags = [];
      var thumbnailObjectUrl = '';

      function escapeHtml(value) {
        return String(value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function normalizeTag(value) {
        var tag = $.trim(value || '');
        if (!tag) {
          return '';
        }
        if (tag.charAt(0) !== '#') {
          tag = '#' + tag;
        }
        return tag;
      }

      function syncHidden() {
        $('#blogTagsHidden').val(JSON.stringify(tags));
      }

      function renderTags() {
        var html = '';
        var i;
        var tag;
        for (i = 0; i < tags.length; i += 1) {
          tag = escapeHtml(tags[i]);
          html += '' +
            '<li class="blog-create-tag-chip" data-index="' + i + '">' +
              '<span class="blog-create-tag-text">' + tag + '</span>' +
              '<button type="button" class="blog-create-tag-remove js-tag-remove" aria-label="태그 삭제">×</button>' +
            '</li>';
        }
        $('#blogTagChips').html(html);
        syncHidden();
      }

      function addTagFromInput() {
        var raw = $('#blogTagInput').val();
        var normalized;

        if (!$.trim(raw || '')) {
          return false;
        }

        normalized = normalizeTag(raw);
        if (!normalized) {
          return false;
        }
        if (tags.indexOf(normalized) > -1) {
          $('#blogTagInput').val('').trigger('focus');
          return false;
        }
        tags.push(normalized);

        $('#blogTagInput').val('').trigger('focus');
        renderTags();
        return true;
      }

      function bindEvents() {
        $('.blog-create-tag-editor').on('click', function () {
          $('#blogTagInput').trigger('focus');
        });

        $('#blogTagInput').on('keydown', function (e) {
          if (e.isComposing || e.keyCode === 229) {
            return;
          }
          if (e.key === 'Enter') {
            e.preventDefault();
            addTagFromInput();
          }
        });

        $('#blogThumbnail').on('change', function (e) {
          var file = e.target.files && e.target.files[0];
          var infoWrapEl = $('#blogThumbnailFileInfo');
          var fileNameEl = $('#blogThumbnailFileName');
          if (thumbnailObjectUrl) {
            URL.revokeObjectURL(thumbnailObjectUrl);
            thumbnailObjectUrl = '';
          }
          if (!file) {
            fileNameEl.text('');
            infoWrapEl.hide();
            return;
          }
          if (!file.type || file.type.indexOf('image/') !== 0) {
            window.alert('이미지 파일만 업로드할 수 있습니다.');
            $(this).val('');
            fileNameEl.text('');
            infoWrapEl.hide();
            return;
          }
          thumbnailObjectUrl = URL.createObjectURL(file);
          fileNameEl.text(file.name || '');
          infoWrapEl.css('display', 'flex');

          {{-- 수정모드에서만 파일 선택 영역을 숨기고 싶다면 아래를 해제하세요.
               $('#blogThumbnailPicker').hide(); --}}
        });

        $('#blogThumbnailViewBtn').on('click', function () {
          if (!thumbnailObjectUrl) {
            return;
          }
          window.open(thumbnailObjectUrl, '_blank', 'noopener,noreferrer');
        });

        $('#blogThumbnailRemoveBtn').on('click', function () {
          $('#blogThumbnail').val('');
          $('#blogThumbnailFileName').text('');
          $('#blogThumbnailFileInfo').hide();
          if (thumbnailObjectUrl) {
            URL.revokeObjectURL(thumbnailObjectUrl);
            thumbnailObjectUrl = '';
          }
        });

        $(document).on('click', '.js-tag-remove', function (e) {
          var index = Number($(this).closest('.blog-create-tag-chip').data('index'));
          e.preventDefault();
          if (Number.isNaN(index)) {
            return;
          }
          tags.splice(index, 1);
          renderTags();
        });

        $('#blogCreateForm .btn.btn-primary').on('click', function () {
          addTagFromInput();
          syncHidden();
          window.alert('현재는 화면 구성 단계입니다.');
        });
      }

      function init() {
        renderTags();
        bindEvents();
      }

      init();
    });
  </script>
@endsection
