@extends('layouts.app')

@section('title', '블로그 목록')

@push('styles')
  <link href="{{ asset('css/blog.css') }}?v={{ filemtime(public_path('css/blog.css')) }}" rel="stylesheet" />
@endpush

@section('content')
  <main class="col-lg-10 content-col blog-page-scope">
    <section class="board-card blog-list-page p-3 p-lg-4 rounded-3 shadow-sm">
      <div class="blog-list-head">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
          <h1 class="blog-list-title">{{ $listTitle ?? '전체 글' }}</h1>
          @if (!empty($writeUrl))
            <button type="button" id="btn_write_top" class="blog-write-top-btn" title="작성" aria-label="작성">
              <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
                <path d="M12.5 7.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
              </svg>
              <span>작성</span>
            </button>
          @endif
        </div>

        <form
          id="form_search"
          name="form_search"
          class="blog-search-wrap"
          action="{{ route("{$group}.index", ['slug' => $slug]) }}"
          method="GET"
          autocomplete="off"
        >
          <select id="search_select_type" name="search_select_type" class="blog-search-select">
            <option value="title" @selected(($filters['search_select_type'] ?? 'title') === 'title')>제목</option>
            <option value="content" @selected(($filters['search_select_type'] ?? 'title') === 'content')>내용</option>
          </select>
          <input
            type="text"
            id="search_keyword"
            name="search_keyword"
            class="blog-search-input"
            value="{{ $filters['search_keyword'] ?? '' }}"
            placeholder="검색어를 입력해 주세요."
          >
          <button type="button" id="btn_search" class="blog-search-btn">검색</button>
        </form>

        <p class="blog-list-total" id="blog_list_total">총 0건</p>
      </div>

      <div id="blogItems" class="blog-items"></div>

      <div class="blog-more-wrap">
        <button type="button" class="btn_more blog-more-btn">+ 목록 더보기</button>
      </div>
    </section>

    <div class="blog-fab-wrap" id="blogFabWrap">
      @if (!empty($writeUrl))
        <button type="button" id="btn_write_fab" class="blog-fab blog-fab-write" title="작성" aria-label="작성">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M12.5 7.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
          </svg>
        </button>
      @endif
    </div>
  </main>

  <div id="blogDetailModal" class="blog-detail-modal" role="dialog" aria-modal="true" aria-labelledby="blogDetailTitle" aria-hidden="true">
    <div class="blog-detail-dialog">
      <div class="blog-detail-head">
        <button type="button" id="blogDetailCloseBtn" class="blog-detail-close" aria-label="닫기" title="닫기">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>
      <div class="blog-detail-body">
        <div id="blogDetailCategory" class="blog-detail-head-category"></div>
        <div class="blog-detail-title-row">
          <h2 id="blogDetailTitle" class="blog-detail-title"></h2>
        </div>
        <div class="blog-detail-meta">
          <span id="blogDetailDate"></span>
        </div>
        <div class="blog-detail-visibility">
          <span id="blogDetailVisibility" class="blog-detail-visibility-badge"></span>
        </div>
        <div id="blogDetailContent" class="blog-detail-content"></div>
        <ul id="blogDetailTags" class="blog-detail-tags"></ul>
        <div class="blog-detail-actions">
          <button type="button" id="blogDetailEditBtn" class="blog-detail-action-btn blog-action-icon-btn is-edit btn" aria-label="수정" title="수정">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
            <span class="visually-hidden">수정</span>
          </button>
          <button type="button" id="blogDetailDeleteBtn" class="blog-detail-action-btn blog-action-icon-btn is-delete btn" aria-label="삭제" title="삭제">
            <i class="bi bi-trash3" aria-hidden="true"></i>
            <span class="visually-hidden">삭제</span>
          </button>
          <button type="button" id="blogDetailPublicBtn" class="blog-detail-action-btn blog-action-icon-btn is-public btn" aria-label="공개설정" title="공개설정">
            <i class="bi bi-eye" aria-hidden="true"></i>
            <span class="visually-hidden">공개설정</span>
          </button>
          <button type="button" id="blogDetailBottomCloseBtn" class="blog-detail-action-btn blog-action-icon-btn is-close btn" aria-label="닫기" title="닫기">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
            <span class="visually-hidden">닫기</span>
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/blog.js') }}?v={{ filemtime(public_path('js/blog.js')) }}" defer></script>  
@endpush

@section('script')
  <script>
    $(function() {
      const listUrl = "{{ route("{$group}.index", ['slug' => $slug]) }}";
      const writeUrl = "{{ $writeUrl ?? '' }}";
      const csrfToken = "{{ csrf_token() }}";
      const initialData = JSON.parse(
        new TextDecoder().decode(
          Uint8Array.from(
            atob("{{ base64_encode(json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}"),
            c => c.charCodeAt(0)
          )
        )
      );
      const canManageVisibility = {{ auth()->check() && auth()->user()?->level === 'admin' ? 'true' : 'false' }};
      window.blogCanManageVisibility = canManageVisibility;

      const state = {
        searchType: String(initialData?.filters?.search_select_type || 'title'),
        searchKeyword: String(initialData?.filters?.search_keyword || ''),
        pagination: initialData?.pagination || {},
        currentDetail: null,
        detailScrollByNoteIdx: {},
        pendingDetailScrollTop: 0,
        isLoadingList: false,
        listUrl: listUrl,
      };

      const $items = $("#blogItems");
      const $moreWrap = $(".blog-more-wrap");
      const $moreButton = $(".btn_more");
      state.$items = $items;
      state.$moreWrap = $moreWrap;
      state.$moreButton = $moreButton;

      $("#search_select_type").val(state.searchType);
      $("#search_keyword").val(state.searchKeyword);

      renderBlogListItems($items, initialData?.items || [], false);
      updateBlogMoreButton($moreWrap, state.pagination);
      $("#blog_list_total").text(`총 ${Number(state.pagination?.total || 0)}건`);

      $("#btn_search").on("click", function() {
        $("#form_search").trigger("submit");
      });

      $("#search_keyword").on("keydown", function(e) {
        if (e.key === "Enter") {
          $("#form_search").trigger("submit");
        }
      });

      if (writeUrl) {
        $("#btn_write_top, #btn_write_fab").on("click", function() {
          location.href = writeUrl;
        });
      }

      $moreButton.on("click", function() {
        if (!state.pagination || !state.pagination.has_more) {
          return;
        }
        const nextPage = Number(state.pagination.current_page || 1) + 1;
        fetchBlogListPage(state, nextPage, true);
      });

      $items.on("click", ".blog-item-more-btn", function(e) {
        e.stopPropagation();
        const showUrl = String($(this).data("show-url") || '');
        if (showUrl) {
          location.href = showUrl;
        }
      });

      $items.on("click", ".blog-item", function(e) {
        if ($(e.target).closest(".blog-item-image-link, .blog-item-thumb, .blog-item-more-btn, button, a").length) {
          return;
        }

        const detailUrl = String($(this).data("show-url") || '');
        if (!detailUrl) {
          return;
        }
        fetchBlogDetail(state, detailUrl);
      });

      $("#blogDetailCloseBtn, #blogDetailBottomCloseBtn").on("click", function() {
        closeBlogDetailModal(state);
      });

      $("#blogDetailEditBtn").on("click", function() {
        const editUrl = state.currentDetail?.actions?.edit_url || '';
        if (editUrl) {
          location.href = editUrl;
        }
      });

      $("#blogDetailDeleteBtn").on("click", function() {
        const deleteUrl = state.currentDetail?.actions?.delete_url || '';
        const noteIdx = Number(state.currentDetail?.note?.idx || 0);

        if (!deleteUrl || noteIdx <= 0) {
          return;
        }

        if (!confirm('삭제하시겠습니까?')) {
          return;
        }

        requestAjax({
          method: 'DELETE',
          url: deleteUrl,
          dataType: 'json',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
          showLoading: true,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          onSuccess: function() {
            closeBlogDetailModal(state);

            const nextTotal = Math.max(0, Number(state.pagination?.total || 0) - 1);
            state.pagination.total = nextTotal;
            $("#blog_list_total").text(`총 ${nextTotal}건`);

            $items.find(`.blog-item[data-note-idx='${noteIdx}']`).remove();
            if ($items.find('.blog-item').length === 0) {
              $items.html('<p class="blog-empty">등록된 글이 없습니다.</p>');
            }

            alert('노트가 삭제되었습니다.');
          },
          onError: function() {
            alert('삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
          }
        });
      });

      $("#blogDetailPublicBtn").on("click", function() {
        const useFlagUrl = state.currentDetail?.actions?.use_flag_url || '';
        const useFlag = String(state.currentDetail?.note?.use_flag || 'N');

        if (!useFlagUrl) {
          return;
        }

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
          showLoading: true,
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          onSuccess: function(res) {
            const nextUseFlag = String(res?.use_flag || (useFlag === 'Y' ? 'N' : 'Y'));
            state.currentDetail.note.use_flag = nextUseFlag;
            state.currentDetail.note.use_flag_label = nextUseFlag === 'Y' ? '공개' : '비공개';
            state.currentDetail.permissions = state.currentDetail.permissions || {};
            state.currentDetail.permissions.can_delete = (nextUseFlag !== 'Y');
            syncBlogListItemVisibility(
              state.currentDetail?.note?.idx,
              nextUseFlag,
              state.currentDetail.note.use_flag_label
            );
            applyBlogDetailState(state, state.currentDetail);
            alert('공개 여부가 변경되었습니다.');
          },
          onError: function() {
            alert('공개여부 변경 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
          }
        });
      });

    });

  </script>
@endsection
