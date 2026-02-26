@extends('layouts.app')

@section('title', '상세내역')

@section('style')
  <style>
    .blog-show-page {
      background: #fff;
      border: 1px solid #e9ecef;
    }

    .blog-show-title {
      margin: 0 0 14px;
      font-size: 34px;
      line-height: 1.3;
      color: #212529;
      word-break: keep-all;
      overflow-wrap: anywhere;
    }

    .blog-show-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px 14px;
      border-bottom: 1px solid #dbe2eb;
      padding-bottom: 14px;
      margin-bottom: 18px;
      color: #6c757d;
      font-size: 15px;
      font-weight: 600;
    }

    .blog-show-visibility {
      margin: -4px 0 14px;
      color: #6c757d;
      font-size: 15px;
      text-align: right;
    }

    .blog-show-visibility-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 52px;
      height: 28px;
      padding: 0 10px;
      border-radius: 10px;
      margin-left: 8px;
      font-size: 13px;
      font-weight: 700;
      border: 1px solid var(--bs-secondary-border-subtle);
      background: var(--bs-secondary-bg-subtle);
      color: var(--bs-secondary-color);
    }

    .blog-show-visibility-badge.is-public {
      border-color: var(--bs-success-border-subtle);
      background: var(--bs-success-bg-subtle);
      color: var(--bs-success-text-emphasis);
    }

    .blog-show-content {
      color: #1f2937;
      font-size: 16px;
      line-height: 1.75;
      word-break: keep-all;
      overflow-wrap: anywhere;
    }

    .blog-show-content p {
      margin: 0 0 14px;
    }

    .blog-show-content h1,
    .blog-show-content h2,
    .blog-show-content h3,
    .blog-show-content h4,
    .blog-show-content h5,
    .blog-show-content h6 {
      margin: 20px 0 10px;
      color: #111827;
      font-weight: 700;
      line-height: 1.35;
    }

    .blog-show-content ul,
    .blog-show-content ol {
      margin: 0 0 14px 20px;
      padding: 0;
    }

    .blog-show-content blockquote {
      margin: 0 0 14px;
      padding: 10px 14px;
      border-left: 4px solid #cfd8e3;
      background: #f8fafc;
      color: #374151;
    }

    .blog-show-content pre {
      margin: 0 0 14px;
      padding: 12px;
      border-radius: 8px;
      background: #111827;
      color: #e5e7eb;
      overflow-x: auto;
    }

    .blog-show-content code {
      background: #f3f4f6;
      color: #111827;
      border-radius: 4px;
      padding: 0 4px;
      font-size: .9em;
    }

    .blog-show-content pre code {
      background: transparent;
      color: inherit;
      padding: 0;
    }

    .blog-show-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 30px 0 0;
      padding: 18px 0 0;
      list-style: none;
      border-top: 1px solid #e3e8ef;
    }

    .blog-show-tags li {
      background: #f8f9fa;
      color: #495057;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 600;
      border: 1px solid #dee2e6;
    }

    .blog-show-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    @media (max-width: 991px) {
      .blog-show-title {
        font-size: 28px;
        margin-bottom: 12px;
      }
    }
  </style>
@endsection

@section('content')
  <section class="col-12 col-lg-8 mx-auto blog-page-scope">
    <div class="board-card blog-show-page p-3 p-lg-4 rounded-3 shadow-sm">
      <h1 class="blog-show-title">{{ $note->subject }}</h1>

      <div class="blog-show-meta">
        <span>{{ $note->group_topic_name }}</span>
        <span>{{ $note->create_datetime?->format('Y-m-d H:i:s') ?? '-' }}</span>
      </div>

      <div class="blog-show-visibility">
        공개여부:
        <span class="blog-show-visibility-badge {{ $useFlag === 'Y' ? 'is-public' : '' }}">{{ config("const.use_flag.{$useFlag}", '-') }}</span>
      </div>

      <article class="blog-show-content">{!! $contentHtml !!}</article>

      @if (($note->tags ?? collect())->isNotEmpty())
        <ul class="blog-show-tags">
          @foreach ($note->tags as $tag)
            <li>#{{ $tag->name }}</li>
          @endforeach
        </ul>
      @endif

      <div class="blog-show-actions">
        <button type="button" class="btn btn-outline-secondary">수정</button>
        <button type="button" class="btn btn-outline-danger">삭제</button>
        <button type="button" class="btn btn-outline-primary">공개설정</button>
        <button type="button" class="btn btn-dark">목록</button>
      </div>
    </div>
  </section>
@endsection
