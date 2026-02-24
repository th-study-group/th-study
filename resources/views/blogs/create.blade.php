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
    $storeUrl = route('blogs.store', ['slug' => $slug]);
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

      <form id="blogCreateForm" method="post" action="{{ $storeUrl }}">
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
          <textarea id="blogContent" name="content" class="form-control tinymce" rows="16" placeholder="내용을 입력하세요."></textarea>
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
          <button type="submit" class="btn btn-primary">적용</button>
          <a href="{{ $listUrl }}" class="btn btn-outline-secondary">목록</a>
        </div>
      </form>
    </div>
  </section>
@endsection

@section('script')
  <script>
    (function ($) {
      if (!$) {
        return;
      }

      var tags = [];

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

        $(document).on('click', '.js-tag-remove', function (e) {
          var index = Number($(this).closest('.blog-create-tag-chip').data('index'));
          e.preventDefault();
          if (Number.isNaN(index)) {
            return;
          }
          tags.splice(index, 1);
          renderTags();
        });

        $('#blogCreateForm').on('submit', function () {
          addTagFromInput();
          syncHidden();
          return window.confirm('적용하시겠습니까?');
        });
      }

      function init() {
        renderTags();
        bindEvents();
      }

      $(init);
    })(window.jQuery);
  </script>
@endsection