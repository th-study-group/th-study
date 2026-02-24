@extends('layouts.app')

@section('title', '블로그 목록')

@section('style')
  <style>
    .global-back-to-top {
      display: none !important;
    }

    .blog-list-page {
      background: #fff;
      border: 1px solid #e9ecef;
    }

    .blog-list-head {
      border-bottom: 1px solid #dee2e6;
      padding-bottom: 18px;
      margin-bottom: 6px;
    }

    .blog-list-title {
      color: #212529;
      font-weight: 700;
      margin: 0;
      font-size: 20px;
      letter-spacing: -0.02em;
    }

    .blog-modal-open {
      position: fixed;
      overflow: hidden;
      width: 100%;
      left: 0;
      right: 0;
    }

    html.blog-modal-open,
    body.blog-modal-open {
      overflow: hidden !important;
      height: 100%;
    }

    html,
    body {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
      width: 0;
      height: 0;
      display: none;
    }

    .blog-search-wrap {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin: 22px 0 12px;
    }

    .blog-search-select,
    .blog-search-input {
      height: 44px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      background: #fff;
      padding: 0 12px;
      color: #495057;
    }

    .blog-search-select {
      width: 128px;
      min-width: 128px;
      flex: 0 0 128px;
      font-size: 16px;
      font-weight: 500;
    }

    .blog-search-input {
      width: calc(100% - 138px);
      min-width: 0;
      flex: 1 1 calc(100% - 138px);
    }

    .blog-search-btn {
      height: 48px;
      border: 1px solid var(--bs-primary);
      background: var(--bs-primary);
      color: #fff;
      border-radius: 4px;
      padding: 0 18px;
      font-weight: 500;
      width: 100%;
      flex: 0 0 100%;
      display: block;
    }

    .blog-list-total {
      color: #6c757d;
      margin: 10px 0 0;
      font-weight: 500;
      font-size: 16px;
    }

    .blog-items {
      margin-top: 8px;
    }

    .blog-item {
      display: flex;
      gap: 18px;
      padding: 18px 0;
      border-bottom: 1px solid #dfdfdf;
      cursor: pointer;
    }

    .blog-item-left {
      flex: 1;
      min-width: 0;
    }

    .blog-item-right {
      width: 280px;
      max-width: 34%;
      flex-shrink: 0;
    }

    .blog-item-thumb {
      width: 100%;
      height: 140px;
      object-fit: cover;
      background: #f0f0f0;
      display: block;
    }

    .blog-item-subject {
      margin: 0 0 6px;
      font-size: 18px;
      line-height: 1.35;
      font-weight: 700;
      color: #222;
      letter-spacing: -0.01em;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
    }

    .blog-item-category {
      margin: 0 0 8px;
      color: #6c757d;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: -0.01em;
    }

    .blog-item-desc {
      margin: 0;
      color: #5d5d5d;
      font-size: 13px;
      line-height: 1.5;
      font-weight: 600;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      overflow: hidden;
    }

    .blog-item-more {
      display: inline-block;
      margin-top: 8px;
      color: #6c757d;
      font-size: 14px;
      text-decoration: none;
      letter-spacing: -0.01em;
    }

    .blog-more-wrap {
      margin-top: 26px;
      padding-bottom: 30px;
    }

    .blog-more-btn {
      width: 100%;
      border: 1px solid #ced4da;
      background: #fff;
      height: 64px;
      color: #495057;
      font-size: 22px;
    }

    .blog-detail-modal {
      position: fixed;
      inset: 0;
      z-index: 2147483000;
      background: rgba(11, 17, 32, 0.5);
      -webkit-backdrop-filter: blur(8px);
      backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
      overscroll-behavior: none;
    }

    .blog-detail-dialog {
      width: min(860px, 100%);
      max-height: 88vh;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
      display: flex;
      flex-direction: column;
    }

    .blog-detail-head {
      position: sticky;
      top: 0;
      z-index: 1;
      background: #fff;
      border-bottom: 1px solid #e2e6eb;
      display: flex;
      justify-content: flex-end;
      padding: 10px 12px;
    }

    .blog-detail-close {
      border: 0;
      background: transparent;
      color: #111;
      font-size: 28px;
      line-height: 1;
      width: 38px;
      height: 38px;
      border-radius: 50%;
    }

    .blog-detail-close:hover {
      background: #f0f3f8;
    }

    .blog-detail-body {
      padding: 14px 22px 24px;
      overflow-y: auto;
      min-height: 0;
      scrollbar-width: none;
      -ms-overflow-style: none;
      overscroll-behavior: contain;
    }

    .blog-detail-body::-webkit-scrollbar {
      width: 0;
      height: 0;
      display: none;
    }

    .blog-detail-title {
      font-size: 28px;
      line-height: 1.4;
      color: #131923;
      margin: 0 0 14px;
      word-break: keep-all;
      overflow-wrap: anywhere;
    }

    .blog-detail-meta {
      display: flex;
      flex-wrap: nowrap;
      gap: 10px 16px;
      margin-bottom: 14px;
      color: #4b5563;
      font-size: 14px;
      border-bottom: 1px solid #ebeff5;
      padding-bottom: 14px;
      align-items: center;
      justify-content: flex-end;
      text-align: right;
    }

    #blogDetailDate,
    #blogDetailCategory {
      text-align: left;
    }

    #blogDetailCategory {
      flex: 1 1 auto;
      min-width: 0;
      text-align: left;
    }

    #blogDetailDate {
      margin-left: 10px;
      text-align: right;
      white-space: nowrap;
    }

    .blog-detail-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 24px 0 0;
      padding: 0;
      list-style: none;
      border-top: 1px solid #ebeff5;
      padding-top: 18px;
    }

    .blog-detail-tags li {
      background: #f8f9fa;
      color: #495057;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 600;
      border: 1px solid #dee2e6;
    }

    .blog-detail-visibility {
      margin: -4px 0 14px;
      color: #4b5563;
      font-size: 14px;
      text-align: right;
    }

    .blog-detail-visibility-badge {
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

    .blog-detail-visibility-badge.is-public {
      border-color: var(--bs-success-border-subtle);
      background: var(--bs-success-bg-subtle);
      color: var(--bs-success-text-emphasis);
    }

    .blog-detail-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 20px;
    }

    .blog-detail-action-btn {
      line-height: 1;
    }

    .blog-detail-content p {
      margin: 0 0 14px;
      font-size: 15px;
      line-height: 1.8;
      color: #1f2937;
      white-space: pre-wrap;
      word-break: keep-all;
      overflow-wrap: anywhere;
    }

    .blog-empty {
      padding: 56px 0;
      color: #999;
      text-align: center;
      font-size: 16px;
      border-bottom: 1px solid #dfdfdf;
    }

    .blog-fab-wrap {
      position: fixed;
      right: 28px;
      bottom: 28px;
      z-index: 99;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .blog-fab {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      border: 1px solid rgba(209, 222, 255, 0.35);
      background: #0f2141;
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      box-shadow: 0 10px 22px rgba(8, 19, 42, 0.25);
    }

    .blog-fab-write {
      background: #4f8cff;
      border-color: #8fb5ff;
      color: #fff;
    }

    .blog-fab-write svg {
      width: 22px;
      height: 22px;
      display: block;
    }

    .blog-fab-top {
      font-size: 24px;
      line-height: 1;
      font-weight: 400;
    }

    @media (max-width: 991px) {
      .blog-list-title {
        font-size: 22px;
      }

      .blog-item {
        gap: 16px;
        padding: 18px 0;
      }

      .blog-search-select {
        font-size: 16px;
      }

      .blog-item-right {
        width: 130px;
        max-width: 130px;
      }

      .blog-item-thumb {
        height: 95px;
      }

      .blog-item-subject {
        font-size: 18px;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
      }

      .blog-item-desc {
        font-size: 14px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
      }

      .blog-item-more {
        margin-top: 8px;
        font-size: 14px;
      }

      .blog-list-total {
        font-size: 16px;
      }

      .blog-detail-modal {
        padding: 0;
      }

      .blog-detail-dialog {
        width: 100%;
        max-height: 100vh;
        height: 100vh;
        border-radius: 0;
      }

      .blog-detail-head {
        padding: 10px;
      }

      .blog-detail-body {
        padding: 12px 14px 18px;
      }

      .blog-detail-title {
        font-size: 22px;
      }

      .blog-detail-meta {
        font-size: 13px;
        gap: 8px 10px;
      }

      .blog-detail-content p {
        font-size: 14px;
        line-height: 1.75;
      }

      .blog-detail-actions {
        flex-wrap: wrap;
      }

      .blog-detail-action-btn {
        height: 34px;
        padding: 0 10px;
        font-size: 13px;
        border-radius: 8px;
      }

      .blog-more-btn {
        height: 48px;
        font-size: 16px;
      }

      .blog-fab-wrap {
        right: 16px;
        bottom: 16px;
      }

      .blog-fab {
        width: 48px;
        height: 48px;
        font-size: 12px;
      }

      .blog-fab-top {
        font-size: 20px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $writeUrl = route('blogs.create', ['slug' => request()->route('slug', 'develop')]);
  @endphp

  <main class="col-lg-10 content-col blog-page-scope">
    <section class="board-card blog-list-page p-3 p-lg-4 rounded-3 shadow-sm">
      <div class="blog-list-head">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
          <h1 class="blog-list-title">전체 글</h1>
          <a href="{{ $writeUrl }}" class="btn btn-dark btn-sm text-nowrap">작성하기</a>
        </div>

        <form id="blogSearchForm" class="blog-search-wrap" autocomplete="off">
          <select id="blogSearchType" class="blog-search-select" name="searchType">
            <option value="title" selected>제목</option>
            <option value="content">내용</option>
          </select>
          <input type="text" id="blogSearchKeyword" class="blog-search-input" name="keyword" placeholder="검색어를 입력해 주세요.">
          <button type="submit" class="blog-search-btn">검색</button>
        </form>

        <p class="blog-list-total" id="blogListTotal">총 0건</p>
      </div>

      <div id="blogItems" class="blog-items"></div>

      <div class="blog-more-wrap">
        <button type="button" id="blogLoadMoreBtn" class="blog-more-btn">+ 목록 더보기</button>
      </div>
    </section>

    <div class="blog-fab-wrap" id="blogFabWrap">
      <a href="{{ $writeUrl }}" class="blog-fab blog-fab-write" title="작성" aria-label="작성">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 20h4l10-10-4-4L4 16v4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"></path>
          <path d="M12.5 7.5l4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
        </svg>
      </a>
      <button type="button" id="blogGoTopBtn" class="blog-fab blog-fab-top" title="맨 위로">^</button>
    </div>
  </main>

  <div id="blogDetailModal" class="blog-detail-modal" role="dialog" aria-modal="true" aria-labelledby="blogDetailTitle">
    <div class="blog-detail-dialog">
      <div class="blog-detail-head">
        <button type="button" id="blogDetailCloseBtn" class="blog-detail-close" aria-label="닫기">×</button>
      </div>
      <div class="blog-detail-body">
        <h2 id="blogDetailTitle" class="blog-detail-title"></h2>
        <div class="blog-detail-meta">
          <span id="blogDetailCategory"></span>
          <span id="blogDetailDate"></span>
        </div>
        <div class="blog-detail-visibility">
          공개여부:
          <span id="blogDetailVisibility" class="blog-detail-visibility-badge">비공개</span>
        </div>
        <div id="blogDetailContent" class="blog-detail-content"></div>
        <ul id="blogDetailTags" class="blog-detail-tags"></ul>
        <div class="blog-detail-actions">
          <button type="button" id="blogDetailEditBtn" class="blog-detail-action-btn btn btn-outline-secondary">수정</button>
          <button type="button" id="blogDetailDeleteBtn" class="blog-detail-action-btn btn btn-outline-danger">삭제</button>
          <button type="button" id="blogDetailPublicBtn" class="blog-detail-action-btn btn btn-outline-primary">공개설정</button>
          <button type="button" id="blogDetailBottomCloseBtn" class="blog-detail-action-btn btn btn-secondary">목록</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    (function ($) {
      if (!$) {
        return;
      }

      var FALLBACK_IMAGE = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/480px-No_image_available.png';
      var PAGE_SIZE = 7;
      var CATEGORY_LABELS = [
        '여행 > 국내여행',
        '맛집 > 국내맛집',
        '개발 > 초딩도 쉽게하는 라라벨',
        '경제 > 초딩도 쉽게 이해하는 경제',
        '개발 > 라라벨 기초'
      ];
      var TAG_SETS = [
        ['#국내여행', '#기차여행', '#당일치기', '#뚜벅이코스', '#여행일정', '#여행기록', '#여행코스', '#주말여행', '#힐링여행', '#국내명소', '#교통팁', '#예산관리', '#사진스팟', '#혼자여행', '#커플여행', '#가족여행', '#여행준비', '#숙소추천', '#맛집코스', '#여행후기'],
        ['#국내맛집', '#카페투어', '#로컬맛집', '#디저트추천', '#브런치맛집', '#점심추천', '#저녁추천', '#가성비맛집', '#핫플맛집', '#카페추천', '#베이커리', '#커피맛집', '#주말맛집', '#데이트코스', '#푸드리뷰', '#맛집기록', '#메뉴추천', '#줄서기팁', '#동네맛집', '#먹방코스'],
        ['#라라벨', '#Laravel', '#PHP', '#입문개발', '#백엔드개발', '#웹개발', '#MVC', '#라우팅', '#컨트롤러', '#블레이드', '#Eloquent', '#마이그레이션', '#시드데이터', '#폼처리', '#인증구현', '#API개발', '#테스트코드', '#개발공부', '#초보개발자', '#코딩기초'],
        ['#경제기초', '#용어정리', '#초보경제', '#생활경제', '#재테크기초', '#금융상식', '#가계부관리', '#소비습관', '#저축습관', '#투자기초', '#물가이해', '#금리이해', '#대출상식', '#부채관리', '#현금흐름', '#경제뉴스', '#정책이해', '#경제공부', '#초보재테크', '#돈관리'],
        ['#라라벨기초', '#웹개발', '#백엔드', '#실습정리', '#프로젝트구성', '#개발환경', '#코드리팩터링', '#예외처리', '#DB설계', '#쿼리최적화', '#뷰템플릿', '#유효성검사', '#파일업로드', '#배포기초', '#버전관리', '#깃사용법', '#개발노트', '#초급강의', '#실전코딩', '#기초강좌']
      ];
      var DETAIL_BLOCKS = [
        '첫 일정은 교통편과 동선을 촘촘히 정리하는 데서 시작했습니다. 실제 이동 시간과 체력 소모를 기준으로 루트를 다시 계산해보니, 지도에서 보던 거리와 체감 거리가 꽤 달랐습니다. 그래서 중간 휴식 지점을 한 번 더 추가했고 결과적으로 전체 일정 만족도가 높아졌습니다.',
        '현장에서 가장 도움됐던 건 미리 정리해둔 체크리스트였습니다. 사진 포인트, 대기 시간, 예산 사용 구간을 분리해서 확인하니 불필요한 동선이 줄었습니다. 특히 사람이 몰리는 시간대를 피하니 같은 장소도 훨씬 여유롭게 즐길 수 있었습니다.',
        '식사와 카페는 유명한 곳만 따라가기보다 이동 동선 근처를 우선으로 잡았습니다. 실제로 이동 효율이 올라가면서 체력 여유가 생겼고, 예상보다 더 많은 장소를 여유롭게 둘러볼 수 있었습니다. 다음 일정에서도 이 방식으로 계획할 생각입니다.',
        '돌아와서 기록을 정리하면서 교통비, 대기 시간, 체류 시간 데이터를 다시 비교했습니다. 다음 방문 시에는 출발 시간을 30분 앞당기면 밀집 구간을 더 잘 피할 수 있겠다는 결론이 나왔습니다. 같은 코스를 처음 가는 분에게도 충분히 재현 가능한 패턴입니다.'
      ];
      var ECONOMY_ARTICLE_REWRITE = [
        '한 달 생계비 250만원을 보호하는 \'생계비 계좌\'가 2026년 2월부터 본격적으로 운영되고 있습니다. 이 제도는 민사집행법 개정에 따라 도입됐고, 금융권 전반에서 상품이 출시되며 실제 이용 단계에 들어갔습니다.',
        '핵심은 계좌 안의 일정 금액을 압류 대상에서 제외한다는 점입니다. 개인은 전 금융권 통합 기준으로 1인 1계좌만 개설할 수 있고, 계좌 잔액 및 월 입금 한도는 250만원으로 제한됩니다. 보호 한도를 넘는 금액은 예비 계좌로 자동 이체되도록 설계됐습니다.',
        '기존 제도에서도 최저생계비 일부는 압류 금지였지만, 실제 현장에서는 채무자가 매달 법원에 해제 신청을 반복해야 하는 불편이 컸습니다. 신청을 놓치면 생활비까지 묶이는 사례가 발생했고, 임차료나 공과금 같은 기본 지출에 즉시 문제가 생긴다는 지적이 이어졌습니다.',
        '과거 압류방지 통장은 주로 복지수급자 등 특정 계층 중심으로 좁게 운영된 반면, 이번 생계비 계좌는 적용 범위를 넓혀 실질적인 경제활동 유지에 초점을 맞췄다는 평가가 나옵니다. 통장이 막혀 임금 수령 자체가 어려워지는 악순환을 줄이려는 취지입니다.',
        '정책상품 성격에 맞춰 수수료 면제 혜택도 함께 제공됩니다. 은행별로 타행이체나 ATM 출금 수수료 감면 조건이 있고, 일부 은행은 모바일 앱을 통한 간편 개설을 지원합니다. 이용 접근성을 높여 제도 도입 효과를 빠르게 체감하도록 한 구성입니다.',
        '요약하면, 이번 제도는 채무 상태에서도 최소한의 생활자금을 안정적으로 운용할 수 있도록 안전장치를 제도화한 것입니다. 압류 절차로 인한 생계 불안을 낮추고, 노동소득 수령과 일상 금융활동을 유지하게 만드는 데 정책 목적이 있습니다.'
      ].join('\n\n');
      var allItems = [];
      var filteredItems = [];
      var renderedCount = 0;
      var isLoading = false;
      var currentKeyword = '';
      var itemSerial = 1;
      var lastFocusedElement = null;
      var lockedScrollTop = 0;
      var baseItems = [
        {
          title: '월미바다열차 타고 떠나는 월미공원 수도권 당일치기 기차 힐링 여행 후기',
          content: '조선시대부터 외국인을 맞이한 우리나라 개항의 역사가 숨쉬는 인천 월미도 월미공원에서 월미바다열차 힐링 걷기 여행 후기를 정리해드려요. 월미도, 월미공원, 인천상륙작전 기념 동상, 월미짱랜드, 월미오락실, 영종도 유람선까지 한 번에 묶어 다녀온 루트입니다.',
          image: 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=960&q=80'
        },
        {
          title: '청주 스타로니아 카페 체코 굴뚝빵 수암골 제빵왕 김탁구 전망대 일몰 후기',
          content: '청주 제빵왕김탁구 팔봉제빵점으로 유명한 수암골 전망대 체코 굴뚝빵으로 유명한 카페에서 아름다운 일몰을 보고 왔습니다. 청주 수암골 카페 추천, 전망대 촬영 포인트, 대중교통 이동 방법까지 한 번에 정리했습니다.',
          image: 'https://images.unsplash.com/photo-1518732714860-b62714ce0c59?auto=format&fit=crop&w=960&q=80'
        },
        {
          title: '울산 간절곶 바다 버스와 KTX 타고 울산 가성비 당일치기 기차 여행 후기',
          content: '울산 간절곶에서 바다버스에서 동해선 전철 태화강에서 수도권 서울 수원역까지 KTX와 무궁화호 가성비 당일치기 여행 동선입니다. 태화강역 환승, 간절곶 버스 노선, 바닷길 산책 코스 정보까지 포함했습니다.',
          image: 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=960&q=80'
        },
        {
          title: '가평 선사시대 역사관과 남이섬 수도권 당일치기 열차 여행 기록',
          content: '수도권 서울 용산역에서 ITX 청춘열차를 타고 가평역 도착 후 선사시대 역사관과 남이섬을 다녀온 일정입니다. 당일치기로 충분히 가능한 루트와 비용, 시간표 중심으로 정리했습니다.',
          image: ''
        },
        {
          title: '부산 흰여울문화마을 감천문화마을 야경 코스 도보 여행 정리',
          content: '부산 영도 흰여울문화마을에서 감천문화마을로 이어지는 도보 여행 코스를 소개합니다. 노을 시간 포인트, 사진 찍기 좋은 장소, 혼잡 시간 회피 팁 등 실제 동선 위주 안내입니다.',
          image: 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=960&q=80'
        },
        {
          title: '강릉 바다열차와 안목해변 카페거리 하루 일정 총정리',
          content: '강릉역에서 바다열차를 타고 안목해변 카페거리까지 이동한 하루 코스입니다. 왕복 교통, 식사 추천, 대기시간 줄이는 팁을 포함해 처음 가는 분도 따라가기 쉬운 일정으로 구성했습니다.',
          image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=960&q=80'
        },
        {
          title: '전주 한옥마을 경기전 야간산책 뚜벅이 여행 가이드',
          content: '전주 한옥마을 중심으로 경기전, 풍남문, 전동성당을 연결해 야간산책한 동선 후기입니다. 숙소 위치 추천과 야간 포토 스팟, 대중교통 기준 이동 팁까지 담았습니다.',
          image: 'https://images.unsplash.com/photo-1470004914212-05527e49370b?auto=format&fit=crop&w=960&q=80'
        }
      ];

      function makeDateTimeString(offset) {
        var base = new Date(Date.UTC(2026, 0, 1, 8, 30, 0));
        var ts = new Date(base.getTime() + (offset * 3701 * 1000));
        var y = ts.getUTCFullYear();
        var m = ts.getUTCMonth() + 1;
        var day = ts.getUTCDate();
        var h = ts.getUTCHours();
        var min = ts.getUTCMinutes();
        var sec = ts.getUTCSeconds();

        return y + '-' +
          (m < 10 ? '0' + m : m) + '-' +
          (day < 10 ? '0' + day : day) + ' ' +
          (h < 10 ? '0' + h : h) + ':' +
          (min < 10 ? '0' + min : min) + ':' +
          (sec < 10 ? '0' + sec : sec);
      }

      function buildDetailContent(source, round) {
        var repeatBlock = [];
        var i;
        var baseText = ECONOMY_ARTICLE_REWRITE;

        for (i = 0; i < 9; i += 1) {
          repeatBlock.push(baseText);
        }

        return [
          repeatBlock.join('\n\n'),
          '이번 회차(' + round + '회차)에서는 실제 체감 기준으로 다시 코스를 재검토했고, 이동 중간마다 기록한 메모를 반영해 일정 완성도를 높였습니다.',
          DETAIL_BLOCKS[round % DETAIL_BLOCKS.length],
          DETAIL_BLOCKS[(round + 1) % DETAIL_BLOCKS.length],
          DETAIL_BLOCKS[(round + 2) % DETAIL_BLOCKS.length]
        ].join('\n\n');
      }

      function lockBodyScroll() {
        lockedScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
        $('html').addClass('blog-modal-open');
        $('body')
          .addClass('blog-modal-open')
          .css('top', -lockedScrollTop + 'px');
      }

      function unlockBodyScroll() {
        $('html').removeClass('blog-modal-open');
        $('body')
          .removeClass('blog-modal-open')
          .css('top', '');
        window.scrollTo(0, lockedScrollTop);
      }

      function makeDummyItems(count) {
        var list = [];
        var i;
        var source;
        var round;
        var row;
        var dateSeed;

        for (i = 0; i < count; i += 1) {
          source = baseItems[i % baseItems.length];
          round = Math.floor(itemSerial / baseItems.length) + 1;
          dateSeed = itemSerial + round;
          row = {
            id: itemSerial,
            title: source.title + ' ' + round,
            content: source.content,
            detailContent: buildDetailContent(source, round),
            hashtags: TAG_SETS[i % TAG_SETS.length],
            category: CATEGORY_LABELS[i % CATEGORY_LABELS.length],
            createdAt: makeDateTimeString(dateSeed),
            updatedAt: itemSerial % 3 === 0 ? makeDateTimeString(dateSeed + 7) : '',
            visibility: itemSerial % 2 === 0 ? '공개' : '비공개',
            image: source.image || FALLBACK_IMAGE,
            link: '/blogs/develop/' + itemSerial + '/show'
          };
          list.push(row);
          itemSerial += 1;
        }

        return list;
      }

      function escapeHtml(value) {
        return String(value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function renderEmpty() {
        $('#blogItems').html('<div class="blog-empty">검색 결과가 없습니다.</div>');
        $('#blogLoadMoreBtn').hide();
      }

      function updateTotal() {
        $('#blogListTotal').text('총 ' + filteredItems.length + '건');
      }

      function buildItemHtml(item) {
        var title = escapeHtml(item.title || '제목 없음');
        var content = escapeHtml(item.content || '내용이 없습니다.');
        var category = escapeHtml(item.category || '기타 > 미분류');
        var image = escapeHtml(item.image || FALLBACK_IMAGE);
        var link = escapeHtml(item.link || '#');
        var itemId = escapeHtml(item.id || '');

        return '' +
          '<article class="blog-item" data-item-id="' + itemId + '" role="button" tabindex="0" aria-label="게시글 상세보기">' +
            '<div class="blog-item-left">' +
              '<p class="blog-item-category">' + category + '</p>' +
              '<h3 class="blog-item-subject">' + title + '</h3>' +
              '<p class="blog-item-desc">' + content + '</p>' +
              '<a class="blog-item-more js-blog-open" href="' + link + '" data-item-id="' + itemId + '">더보기 &rsaquo;</a>' +
            '</div>' +
            '<div class="blog-item-right">' +
              '<img class="blog-item-thumb" src="' + image + '" alt="썸네일" onerror="this.onerror=null;this.src=\'' + FALLBACK_IMAGE + '\';">' +
            '</div>' +
          '</article>';
      }

      function renderDetailTags(tags) {
        var html = '';
        var i;
        var safeTag;

        for (i = 0; i < tags.length; i += 1) {
          safeTag = escapeHtml(tags[i]);
          html += '<li>' + safeTag + '</li>';
        }

        return html;
      }

      function renderDetailContent(content) {
        var paragraphs = String(content || '').split(/\n{2,}/);
        var html = '';
        var i;
        var text;

        for (i = 0; i < paragraphs.length; i += 1) {
          text = $.trim(paragraphs[i]);
          if (!text) {
            continue;
          }
          html += '<p>' + escapeHtml(text) + '</p>';
        }

        return html;
      }

      function findItemById(id) {
        var i;
        for (i = 0; i < allItems.length; i += 1) {
          if (String(allItems[i].id) === String(id)) {
            return allItems[i];
          }
        }
        return null;
      }

      function openDetailModal(item) {
        var displayDate = item.updatedAt || item.createdAt || '-';
        var category = item.category || '기타 > 미분류';
        var tags = item.hashtags || [];
        var detailContent = item.detailContent || item.content || '';
        var visibility = item.visibility || '비공개';

        $('#blogDetailTitle').text(item.title || '제목 없음');
        $('#blogDetailDate').text(displayDate);
        $('#blogDetailCategory').text(category);
        $('#blogDetailVisibility')
          .text(visibility)
          .toggleClass('is-public', visibility === '공개');
        $('#blogDetailTags').html(renderDetailTags(tags));
        $('#blogDetailContent').html(renderDetailContent(detailContent));
        $('.blog-detail-body').scrollTop(0);
        $('#blogDetailModal').css('display', 'flex').hide().fadeIn(140);
        lockBodyScroll();
      }

      function closeDetailModal() {
        $('#blogDetailModal').fadeOut(120);
        unlockBodyScroll();
        if (lastFocusedElement && lastFocusedElement.focus) {
          lastFocusedElement.focus();
        }
      }

      function appendItems(count) {
        var end;
        var chunk;
        var html = '';
        var i;

        if (isLoading) {
          return;
        }

        if (renderedCount >= filteredItems.length) {
          if (!currentKeyword) {
            allItems = allItems.concat(makeDummyItems(PAGE_SIZE));
            filteredItems = allItems.slice(0);
            updateTotal();
          } else {
            $('#blogLoadMoreBtn').hide();
            return;
          }
        }

        isLoading = true;
        end = renderedCount + count;
        if (end > filteredItems.length) {
          end = filteredItems.length;
        }

        chunk = filteredItems.slice(renderedCount, end);
        for (i = 0; i < chunk.length; i += 1) {
          html += buildItemHtml(chunk[i]);
        }

        $('#blogItems').append(html);
        renderedCount = end;

        if (renderedCount >= filteredItems.length) {
          if (currentKeyword) {
            $('#blogLoadMoreBtn').hide();
          } else {
            $('#blogLoadMoreBtn').show();
          }
        } else {
          $('#blogLoadMoreBtn').show();
        }

        isLoading = false;
      }

      function resetAndRender() {
        renderedCount = 0;
        $('#blogItems').empty();
        updateTotal();

        if (!filteredItems.length) {
          renderEmpty();
          return;
        }

        appendItems(PAGE_SIZE);
      }

      function applySearch() {
        var keyword = $.trim($('#blogSearchKeyword').val()).toLowerCase();
        var type = $('#blogSearchType').val();
        currentKeyword = keyword;

        filteredItems = $.grep(allItems, function (item) {
          var source = '';

          if (type === 'content') {
            source = item.content || '';
          } else {
            source = item.title || '';
          }

          source = String(source).toLowerCase();
          if (!keyword) {
            return true;
          }

          return source.indexOf(keyword) > -1;
        });

        resetAndRender();
      }

      function bindEvents() {
        $('#blogSearchForm').on('submit', function (e) {
          e.preventDefault();
          applySearch();
        });

        $('#blogLoadMoreBtn').on('click', function () {
          appendItems(PAGE_SIZE);
        });

        $('#blogGoTopBtn').on('click', function () {
          $('html, body').animate({ scrollTop: 0 }, 260);
        });

        $(document).on('click', '.blog-item', function (e) {
          var itemId;
          var item;

          if ($(e.target).closest('.js-blog-open').length) {
            return;
          }

          e.preventDefault();
          itemId = $(this).data('item-id');
          item = findItemById(itemId);
          if (!item) {
            return;
          }
          lastFocusedElement = this;
          openDetailModal(item);
        });

        $(document).on('keydown', '.blog-item', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
          }
        });

        $('#blogDetailCloseBtn').on('click', function () {
          closeDetailModal();
        });

        $('#blogDetailBottomCloseBtn').on('click', function () {
          closeDetailModal();
        });

        $('#blogDetailEditBtn').on('click', function () {
          window.alert('수정 기능은 다음 단계에서 연결 예정입니다.');
        });

        $('#blogDetailDeleteBtn').on('click', function () {
          window.alert('삭제 기능은 다음 단계에서 연결 예정입니다.');
        });

        $('#blogDetailPublicBtn').on('click', function () {
          window.alert('공개설정 기능은 다음 단계에서 연결 예정입니다.');
        });

        $('#blogDetailModal').on('click', function (e) {
          if (e.target === this) {
            closeDetailModal();
          }
        });

        $(document).on('keydown', function (e) {
          if (e.key === 'Escape' && $('#blogDetailModal').is(':visible')) {
            closeDetailModal();
          }
        });
      }

      function init() {
        $('#blogDetailModal').appendTo('body');
        allItems = makeDummyItems(PAGE_SIZE * 3);
        filteredItems = allItems.slice(0);
        bindEvents();
        resetAndRender();
      }

      $(init);
    })(window.jQuery);
  </script>
@endsection
