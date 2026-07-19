@extends('layouts.app')

@section('title', '블로그 목록')

@section('style')
  <style>
    .blog-detail-adfit {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
  </style>
@endsection

@push('styles')
  <link href="{{ asset('css/blog.css') }}?v={{ filemtime(public_path('css/blog.css')) }}" rel="stylesheet" />
@endpush

@section('content')
  <div id="blogIndexPageShell" class="blog-index-page-shell col-lg-10 content-col blog-page-scope">
    <main>
      <section class="board-card blog-list-page p-3 p-lg-4 rounded-3 shadow-sm">
        <div class="blog-list-head">
        <div class="d-flex align-items-center justify-content-between gap-2 blog-list-title-row">
          <div class="blog-list-title-wrap">
            <h1 class="blog-list-title">{{ $listTitle ?? '전체 글' }}</h1>
            @if (!empty($listDescription))
              <p class="blog-list-description-inline d-none d-md-inline-block mb-0">{{ $listDescription }}</p>
              <div class="blog-list-description-mobile d-md-none">
                <button
                  type="button"
                  id="blogDescToggle"
                  class="blog-list-description-toggle"
                  aria-label="설명 보기"
                  aria-expanded="false"
                  aria-controls="blogDescTooltip"
                >?</button>
                <div id="blogDescTooltip" class="blog-list-description-tooltip" role="tooltip" hidden>
                  {{ $listDescription }}
                </div>
              </div>
            @endif
          </div>
          <button type="button" id="btn_write_top" class="blog-write-top-btn" title="작성" aria-label="작성">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
              <path d="M12.5 7.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
            </svg>
            <span>작성</span>
          </button>
        </div>

        <form
          id="form_search"
          name="form_search"
          class="blog-search-wrap"
          action="{{ route("{$group}.index", ['slug' => $slug]) }}"
          method="GET"
          autocomplete="off"
        >
          <input type="hidden" id="topic_filter" name="search_topic" value="{{ $selectedTopic ?? '' }}">

          <div class="blog-search-panel">
            <button type="button" id="btn_filter_sheet" class="blog-filter-sheet-trigger">
              <span class="blog-filter-sheet-trigger__label" id="blogFilterTriggerLabel">카테고리 / 주제 선택</span>
              <span class="blog-filter-sheet-trigger__summary is-hidden" id="blogFilterSummaryText"></span>
              <span class="blog-filter-sheet-trigger__action" aria-hidden="true">
                <i class="bi bi-box-arrow-up-right"></i>
              </span>
            </button>

            <div class="blog-search-input-row">
              <div class="blog-search-type-dropdown" id="blogSearchTypeDropdown">
                <button
                  type="button"
                  id="blogSearchTypeToggle"
                  class="blog-search-select blog-search-select-box blog-search-type-toggle"
                  aria-haspopup="listbox"
                  aria-expanded="false"
                  aria-controls="blogSearchTypeMenu"
                >
                  <span id="blogSearchTypeLabel">제목</span>
                </button>
                <div id="blogSearchTypeMenu" class="blog-search-type-menu" role="listbox" aria-label="검색 타입 선택" hidden>
                  <button type="button" class="blog-search-type-option" data-value="title" role="option">제목</button>
                  <button type="button" class="blog-search-type-option" data-value="content" role="option">내용</button>
                </div>
                <input
                  type="hidden"
                  id="search_select_type"
                  name="search_select_type"
                  value="{{ $filters['search_select_type'] ?? 'title' }}"
                >
              </div>
              <input
                type="text"
                id="search_keyword"
                name="search_keyword"
                class="blog-search-input"
                value="{{ $filters['search_keyword'] ?? '' }}"
                placeholder="검색어를 입력해 주세요."
              >
            </div>

            <div class="blog-search-actions">
              <button type="button" id="btn_search_reset" class="blog-search-reset-btn">
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                <span>초기화</span>
              </button>
              <button type="button" id="btn_search" class="blog-search-btn">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>검색</span>
              </button>
            </div>
          </div>
        </form>

        <div class="blog-refresh-guide" aria-live="polite">
          <div class="blog-refresh-guide-main">
            <button type="button" id="btn_refresh_top" class="blog-refresh-btn" title="새로고침" aria-label="새로고침">
              <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
            </button>
            <div class="blog-refresh-copy">
              <p class="blog-refresh-title">최신 글 다시 불러오기</p>
              <p class="blog-refresh-hint" id="blogRefreshHint">새로고침 버튼으로 최신 글을 다시 불러올 수 있습니다.</p>
            </div>
          </div>
          <p class="blog-refresh-meta">
            마지막 갱신
            <time id="blogRefreshTime" datetime="">방금 전</time>
          </p>
        </div>

        <div class="text-center my-3 d-none d-md-block">
          <x-adfit
            :unit="config('adfit.pc.rectangle.unit')"
            :width="config('adfit.pc.rectangle.width')"
            :height="config('adfit.pc.rectangle.height')" />
        </div>

        <div class="text-center my-3 d-block d-md-none">
          <x-adfit
            :unit="config('adfit.mobile.rectangle.unit')"
            :width="config('adfit.mobile.rectangle.width')"
            :height="config('adfit.mobile.rectangle.height')" />
        </div>

        <p class="blog-list-total" id="blog_list_total">총 0건</p>
        </div>

        <div id="blogItems" class="blog-items"></div>

        <div class="blog-more-wrap">
          <button type="button" class="btn_more blog-more-btn">+ 목록 더보기</button>
        </div>
      </section>

      <div class="blog-fab-wrap" id="blogFabWrap">
        <button type="button" id="btn_refresh_fab" class="blog-fab blog-fab-refresh" title="새로고침" aria-label="새로고침">
          <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
        </button>
        <button type="button" id="btn_write_fab" class="blog-fab blog-fab-write" title="작성" aria-label="작성">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M12.5 7.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
          </svg>
        </button>
      </div>
    </main>
  </div>

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

        <div class="blog-detail-adfit text-center my-3">
          <x-adfit
            :unit="config('adfit.common.square.unit')"
            :width="config('adfit.common.square.width')"
            :height="config('adfit.common.square.height')" />
        </div>

        <div id="blogDetailContent" class="blog-detail-content"></div>
        <section id="blogDetailRelatedWrap" class="blog-detail-related" aria-label="관련 글 목록">
          <h3 id="blogDetailRelatedTitle" class="blog-detail-related-title"></h3>
          <ul id="blogDetailRelatedList" class="blog-detail-related-list"></ul>
        </section>
        <ul id="blogDetailTags" class="blog-detail-tags"></ul>
      </div>
      <div class="blog-detail-footer">
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
          <button type="button" id="blogDetailShareBtn" class="blog-detail-action-btn blog-action-icon-btn is-public btn" aria-label="공유하기" title="공유하기">
            <i class="bi bi-link-45deg" aria-hidden="true"></i>
            <span class="visually-hidden">공유하기</span>
          </button>
          <button type="button" id="blogDetailBottomCloseBtn" class="blog-detail-action-btn blog-action-icon-btn is-close btn" aria-label="닫기" title="닫기">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
            <span class="visually-hidden">닫기</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div id="blogFilterSheet" class="blog-filter-sheet" aria-hidden="true">
    <div class="blog-filter-sheet__backdrop"></div>
    <div class="blog-filter-sheet__dialog" role="dialog" aria-modal="true" aria-labelledby="blogFilterSheetTitle">
      <div class="blog-filter-sheet__head">
        <strong id="blogFilterSheetTitle" class="blog-filter-sheet__title">카테고리 / 주제 선택</strong>
        <button type="button" class="blog-filter-sheet__close" data-filter-close aria-label="닫기">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>
      <div class="blog-filter-sheet__body">
        <section class="blog-filter-sheet__section">
          <p class="blog-filter-sheet__label">카테고리</p>
          <div id="blogCategoryOptions" class="blog-filter-sheet__grid"></div>
        </section>
        <section class="blog-filter-sheet__section">
          <div class="blog-filter-sheet__label-row">
            <p class="blog-filter-sheet__label mb-0">주제</p>
            <!--<span class="blog-filter-sheet__hint">주제는 선택하지 않아도 됩니다.</span>-->
          </div>
          <div id="blogTopicOptions" class="blog-filter-sheet__grid is-topic"></div>
          <p id="blogTopicEmpty" class="blog-filter-sheet__empty" hidden>선택 가능한 주제가 없습니다.</p>
        </section>
      </div>
      <div class="blog-filter-sheet__foot">
        <button type="button" id="btn_filter_apply" class="blog-filter-sheet__primary">적용</button>
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
      const userLevel = "{{ auth()->user()?->level }}";
      const listUrl = "{{ route("{$group}.index", ['slug' => $slug]) }}";
      const writeUrl = "{{ $writeUrl ?? '' }}";
      const filterBaseUrl = "{{ url($group) }}";
      const topicsByCategoryUrl = "{{ route("{$group}.topics.category") }}";
      const csrfToken = "{{ csrf_token() }}";
      const initialCategoryCode = "{{ $slug ?? '' }}";
      const initialTopicValue = "{{ $selectedTopic ?? '' }}";
      const categoryItems = JSON.parse(
        new TextDecoder().decode(
          Uint8Array.from(
            atob("{{ base64_encode(json_encode($categoryItems ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}"),
            c => c.charCodeAt(0)
          )
        )
      );
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


      const formatRefreshDateTime = function(date) {
        if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
          return '';
        }

        return new Intl.DateTimeFormat('ko-KR', {
          month: 'numeric',
          day: 'numeric',
          hour: 'numeric',
          minute: '2-digit',
          hour12: true,
        }).format(date);
      };

      const resolveRefreshHintText = function() {
        const isStandalone = typeof isStandalonePwa === 'function' && isStandalonePwa();
        const isTouchDevice = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;

        if (isStandalone) {
          return '설치된 앱 화면에서는 새로고침 버튼으로 최신 글을 다시 불러오세요.';
        }

        if (isTouchDevice) {
          return '모바일에서는 화면을 아래로 끌어 새로고침하거나 버튼을 눌러 최신 글을 불러올 수 있어요.';
        }

        return 'PC에서는 F5 또는 Ctrl+R, 이 버튼으로 최신 글을 다시 불러올 수 있어요.';
      };

      const applyRefreshGuideState = function() {
        $('#blogRefreshHint').text(resolveRefreshHintText());
      };

      const updateRefreshTime = function(date) {
        const refreshDate = date instanceof Date ? date : new Date();
        const formatted = formatRefreshDateTime(refreshDate);
        const isoString = Number.isNaN(refreshDate.getTime()) ? '' : refreshDate.toISOString();

        $('#blogRefreshTime')
          .attr('datetime', isoString)
          .text(formatted || '방금 전');
      };

      const state = {
        searchType: String(initialData?.filters?.search_select_type || 'title'),
        searchKeyword: String(initialData?.filters?.search_keyword || ''),
        selectedCategoryCode: String(initialCategoryCode || ''),
        selectedTopicValue: String(initialTopicValue || ''),
        pendingCategoryCode: String(initialCategoryCode || ''),
        pendingTopicValue: String(initialTopicValue || ''),
        pagination: initialData?.pagination || {},
        currentDetail: null,
        initialMetaKeywords: String($('meta[name="keywords"]').attr('content') || ''),
        currentMetaKeywords: String($('meta[name="keywords"]').attr('content') || ''),
        detailScrollByNoteIdx: {},
        pendingDetailScrollTop: 0,
        isLoadingList: false,
        listUrl: listUrl,
      };

      initShareCopyButtons('#blogDetailShareBtn', {
        getUrl: function () {
          return String(state.currentDetail?.actions?.show_url || '').trim();
        }
      });

      const $items = $("#blogItems");
      const $moreWrap = $(".blog-more-wrap");
      const $moreButton = $(".btn_more");
      const $filterSheet = $("#blogFilterSheet");
      const $categoryOptions = $("#blogCategoryOptions");
      const $topicOptions = $("#blogTopicOptions");
      const $topicEmpty = $("#blogTopicEmpty");
      const $searchTypeDropdown = $("#blogSearchTypeDropdown");
      const $searchTypeToggle = $("#blogSearchTypeToggle");
      const $searchTypeMenu = $("#blogSearchTypeMenu");
      const $searchTypeLabel = $("#blogSearchTypeLabel");
      const topicItemsByCategory = {};
      const topicLoadingByCategory = {};
      state.$items = $items;
      state.$moreWrap = $moreWrap;
      state.$moreButton = $moreButton;

      const resolveSearchTypeLabel = function(value) {
        if (String(value || '') === 'content') {
          return '내용';
        }

        return '제목';
      };

      const syncSearchTypeUi = function(value) {
        const normalized = String(value || 'title') === 'content' ? 'content' : 'title';
        state.searchType = normalized;
        $("#search_select_type").val(normalized);
        $searchTypeLabel.text(resolveSearchTypeLabel(normalized));

        const $options = $searchTypeMenu.find(".blog-search-type-option");
        $options.attr("aria-selected", "false").removeClass("is-active");
        $options.filter(`[data-value="${normalized}"]`).attr("aria-selected", "true").addClass("is-active");
      };

      const closeSearchTypeMenu = function() {
        $searchTypeMenu.attr("hidden", "hidden");
        $searchTypeToggle.attr("aria-expanded", "false");
        $searchTypeDropdown.removeClass("is-open");
      };

      const openSearchTypeMenu = function() {
        $searchTypeMenu.removeAttr("hidden");
        $searchTypeToggle.attr("aria-expanded", "true");
        $searchTypeDropdown.addClass("is-open");
      };

      const findCategoryName = function(categoryCode) {
        const matched = categoryItems.find(function(item) {
          return String(item?.code || '') === String(categoryCode || '');
        });

        return String(matched?.name || '');
      };

      const getTopicItems = function(categoryCode) {
        if (!categoryCode) {
          return [];
        }

        return Array.isArray(topicItemsByCategory?.[categoryCode])
          ? topicItemsByCategory[categoryCode]
          : [];
      };

      const loadTopicsByCategory = function(categoryCode) {
        const normalizedCategoryCode = String(categoryCode || '').trim();

        if (normalizedCategoryCode === '') {
          return Promise.resolve([]);
        }

        if (Array.isArray(topicItemsByCategory[normalizedCategoryCode])) {
          return Promise.resolve(topicItemsByCategory[normalizedCategoryCode]);
        }

        if (topicLoadingByCategory[normalizedCategoryCode]) {
          return topicLoadingByCategory[normalizedCategoryCode];
        }

        topicLoadingByCategory[normalizedCategoryCode] = new Promise(function(resolve) {
          requestAjax({
            method: 'GET',
            url: topicsByCategoryUrl,
            dataType: 'json',
            data: {
              category: normalizedCategoryCode,
            },
            showLoading: true,
            onSuccess: function(res) {
              const topics = Array.isArray(res?.topics) ? res.topics : [];
              topicItemsByCategory[normalizedCategoryCode] = topics;
              resolve(topics);
            },
            onError: function() {
              topicItemsByCategory[normalizedCategoryCode] = [];
              resolve([]);
            },
            onComplete: function() {
              delete topicLoadingByCategory[normalizedCategoryCode];
            },
          });
        });

        return topicLoadingByCategory[normalizedCategoryCode];
      };

      const findTopicName = function(categoryCode, topicValue) {
        const topics = getTopicItems(categoryCode);
        const matched = topics.find(function(item) {
          return String(item?.idx || '') === String(topicValue || '');
        });

        return String(matched?.name || '');
      };

      const updateFilterSummary = function() {
        const categoryName = findCategoryName(state.selectedCategoryCode) || '전체';
        const topicName = findTopicName(state.selectedCategoryCode, state.selectedTopicValue) || '';
        const summaryText = topicName ? `${categoryName} > ${topicName}` : categoryName;
        const isDefaultSelection = String(state.selectedCategoryCode || '').trim() === ''
          && String(state.selectedTopicValue || '').trim() === '';

        $("#blogFilterSummaryText")
          .text(isDefaultSelection ? '' : summaryText)
          .toggleClass("is-hidden", isDefaultSelection);
        $("#blogFilterTriggerLabel").toggleClass("d-none", !isDefaultSelection);
        $("#topic_filter").val(state.selectedTopicValue);
      };

      const renderCategoryOptions = function() {
        const fragments = [];
        const isAllActive = state.pendingCategoryCode === '';

        fragments.push(
          `<button type="button" class="blog-filter-line-btn ${isAllActive ? 'is-active' : ''}" data-category-code="">전체</button>`
        );

        categoryItems.forEach(function(category) {
          const code = String(category?.code || '');
          const name = String(category?.name || code);
          const isActive = code === state.pendingCategoryCode;

          fragments.push(
            `<button type="button" class="blog-filter-line-btn ${isActive ? 'is-active' : ''}" data-category-code="${escapeHtmlText(code)}">${escapeHtmlText(name)}</button>`
          );
        });

        $categoryOptions.html(fragments.join(''));
      };

      const renderTopicOptions = function() {
        const topics = getTopicItems(state.pendingCategoryCode);

        if (!state.pendingCategoryCode || topics.length === 0) {
          $topicOptions.empty();
          $topicEmpty.prop("hidden", false);
          return;
        }

        const fragments = [
          `<button type="button" class="blog-filter-line-btn ${state.pendingTopicValue === '' ? 'is-active' : ''}" data-topic-value="">전체</button>`
        ];

        topics.forEach(function(topic) {
          const value = String(topic?.idx || '');
          const name = String(topic?.name || value);
          const isActive = value === state.pendingTopicValue;

          fragments.push(
            `<button type="button" class="blog-filter-line-btn ${isActive ? 'is-active' : ''}" data-topic-value="${escapeHtmlText(value)}">${escapeHtmlText(name)}</button>`
          );
        });

        $topicOptions.html(fragments.join(''));
        $topicEmpty.prop("hidden", true);
      };

      const openFilterSheet = function() {
        if ($filterSheet.length && !$filterSheet.parent().is('body')) {
          $filterSheet.appendTo('body');
        }

        state.pendingCategoryCode = state.selectedCategoryCode;
        state.pendingTopicValue = state.selectedTopicValue;
        renderCategoryOptions();
        renderTopicOptions();
        loadTopicsByCategory(state.pendingCategoryCode).then(function() {
          renderTopicOptions();
          updateFilterSummary();
        });
        $filterSheet.attr("aria-hidden", "false").addClass("is-open");
        $("html, body").addClass("blog-filter-sheet-open");
      };

      const closeFilterSheet = function() {
        $filterSheet.attr("aria-hidden", "true").removeClass("is-open");
        $("html, body").removeClass("blog-filter-sheet-open");
      };

      const buildCategoryUrl = function(categoryCode, topicValue) {
        const trimmedCategoryCode = String(categoryCode || '').trim();
        let nextUrl = trimmedCategoryCode ? `${filterBaseUrl}/${encodeURIComponent(trimmedCategoryCode)}` : filterBaseUrl;

        if (String(topicValue || '').trim() !== '') {
          nextUrl += `?search_topic=${encodeURIComponent(String(topicValue).trim())}`;
        }

        return nextUrl;
      };

      const $descToggle = $("#blogDescToggle");
      const $descTooltip = $("#blogDescTooltip");
      if ($descToggle.length && $descTooltip.length) {
        const closeDescriptionTooltip = function() {
          $descToggle.attr("aria-expanded", "false");
          $descTooltip.attr("hidden", "hidden");
        };

        const openDescriptionTooltip = function() {
          $descToggle.attr("aria-expanded", "true");
          $descTooltip.removeAttr("hidden");
        };

        $descToggle.on("click", function(e) {
          e.preventDefault();
          e.stopPropagation();
          if ($descToggle.attr("aria-expanded") === "true") {
            closeDescriptionTooltip();
            return;
          }
          openDescriptionTooltip();
        });

        $(document).on("click", function(e) {
          if ($(e.target).closest("#blogDescToggle, #blogDescTooltip").length) {
            return;
          }
          closeDescriptionTooltip();
        });

        $(document).on("keydown", function(e) {
          if (e.key === "Escape") {
            closeDescriptionTooltip();
          }
        });
      }

      syncSearchTypeUi(state.searchType);
      $("#search_keyword").val(state.searchKeyword);
      updateFilterSummary();
      loadTopicsByCategory(state.selectedCategoryCode).then(function() {
        updateFilterSummary();
      });

      applyRefreshGuideState();
      updateRefreshTime(new Date());

      renderBlogListItems($items, initialData?.items || [], false);
      updateBlogMoreButton($moreWrap, state.pagination);
      $("#blog_list_total").text(`총 ${Number(state.pagination?.total || 0)}건`);

      $("#btn_filter_sheet").on("click", function() {
        openFilterSheet();
      });

      $filterSheet.on("click", ".blog-filter-sheet__close", function() {
        closeFilterSheet();
      });

      $categoryOptions.on("click", "[data-category-code]", function() {
        state.pendingCategoryCode = String($(this).data("category-code") || '');
        state.pendingTopicValue = '';
        renderCategoryOptions();
        renderTopicOptions();
        loadTopicsByCategory(state.pendingCategoryCode).then(function() {
          renderTopicOptions();
        });
      });

      $topicOptions.on("click", "[data-topic-value]", function() {
        state.pendingTopicValue = String($(this).data("topic-value") || '');
        renderTopicOptions();
      });

      $("#btn_filter_apply").on("click", function() {
        const nextUrl = buildCategoryUrl(state.pendingCategoryCode, state.pendingTopicValue);
        const currentUrl = buildCategoryUrl(state.selectedCategoryCode, state.selectedTopicValue);

        state.selectedCategoryCode = state.pendingCategoryCode;
        state.selectedTopicValue = state.pendingTopicValue;
        updateFilterSummary();
        closeFilterSheet();

        if (nextUrl !== currentUrl) {
          window.location.href = nextUrl;
        }
      });

      $("#btn_search").on("click", function() {
        $("#form_search").trigger("submit");
      });

      $searchTypeToggle.on("click", function(e) {
        e.preventDefault();
        if ($searchTypeDropdown.hasClass("is-open")) {
          closeSearchTypeMenu();
          return;
        }
        openSearchTypeMenu();
      });

      $searchTypeMenu.on("click", ".blog-search-type-option", function(e) {
        e.preventDefault();
        const nextValue = String($(this).data("value") || 'title');
        syncSearchTypeUi(nextValue);
        closeSearchTypeMenu();
      });

      $(document).on("click", function(e) {
        if ($(e.target).closest("#blogSearchTypeDropdown").length) {
          return;
        }
        closeSearchTypeMenu();
      });

      $(document).on("keydown", function(e) {
        if (e.key === "Escape") {
          closeSearchTypeMenu();
        }
      });

      $("#btn_search_reset").on("click", function() {
        syncSearchTypeUi('title');
        state.selectedCategoryCode = '';
        state.selectedTopicValue = '';
        state.pendingCategoryCode = '';
        state.pendingTopicValue = '';
        state.searchKeyword = '';
        $("#topic_filter").val("");
        $("#search_keyword").val("");
        updateFilterSummary();
        window.location.href = buildCategoryUrl('', '');
      });

      $("#search_keyword").on("keydown", function(e) {
        if (e.key === "Enter") {
          $("#form_search").trigger("submit");
        }
      });

      if (writeUrl) {
        $("#btn_write_top, #btn_write_fab").on("click", function() {
          if (userLevel !== 'admin') {
            alert('글 작성 권한이 없습니다.');
            return;
          }
          
          location.href = writeUrl;
        });
      }
      $('#btn_refresh_top, #btn_refresh_fab').on('click', function() {
        fetchBlogListPage(state, 1, false);
      });

      if (window.matchMedia) {
        const touchMedia = window.matchMedia('(pointer: coarse)');
        const standaloneMedia = window.matchMedia('(display-mode: standalone)');
        const refreshGuideHandler = function() {
          applyRefreshGuideState();
        };

        if (typeof touchMedia.addEventListener === 'function') {
          touchMedia.addEventListener('change', refreshGuideHandler);
          standaloneMedia.addEventListener('change', refreshGuideHandler);
        } else if (typeof touchMedia.addListener === 'function') {
          touchMedia.addListener(refreshGuideHandler);
          standaloneMedia.addListener(refreshGuideHandler);
        }
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

      $("#blogDetailRelatedList").on("click", ".js-blog-detail-related-open", function(e) {
        e.preventDefault();
        const detailUrl = String($(this).data("show-url") || '');
        if (!detailUrl) {
          return;
        }
        fetchBlogDetail(state, detailUrl);
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
