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
      font-size: 20px;
      font-weight: 600;
    }

    .blog-show-meta-category {
      text-align: left;
      min-width: 0;
    }

    .blog-show-meta-date {
      text-align: right;
      white-space: nowrap;
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

    .blog-show-content p {
      margin: 0 0 14px;
      font-size: 18px;
      line-height: 1.75;
      color: #1f2937;
      word-break: keep-all;
      overflow-wrap: anywhere;
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
    }

    .blog-show-action-btn {
      text-decoration: none;
    }

    @media (max-width: 991px) {
      .blog-show-title {
        font-size: 28px;
        margin-bottom: 12px;
      }

      .blog-show-meta {
        font-size: 15px;
      }

      .blog-show-visibility {
        font-size: 14px;
      }

      .blog-show-content p {
        font-size: 16px;
        line-height: 1.8;
      }

      .blog-show-actions {
        flex-wrap: wrap;
      }

      .blog-show-action-btn {
        height: 34px;
        padding: 0 10px;
        font-size: 13px;
        border-radius: 8px;
      }
    }
  </style>
@endsection

@section('content')
  @php
    $idx = (int) request()->route('idx', 1);
    $slug = (string) request()->route('slug', 'develop');

    $baseTitles = [
      '월미바다열차 타고 떠나는 월미공원 수도권 당일치기 기차 힐링 여행 후기',
      '청주 스타로니아 카페 체코 굴뚝빵 수암골 제빵왕 김탁구 전망대 일몰 후기',
      '울산 간절곶 바다 버스와 KTX 타고 울산 가성비 당일치기 기차 여행 후기',
      '가평 선사시대 역사관과 남이섬 수도권 당일치기 열차 여행 기록',
      '부산 흰여울문화마을 감천문화마을 야경 코스 도보 여행 정리',
      '강릉 바다열차와 안목해변 카페거리 하루 일정 총정리',
      '전주 한옥마을 경기전 야간산책 뚜벅이 여행 가이드',
    ];

    $categories = [
      '여행 > 국내여행',
      '맛집 > 국내맛집',
      '개발 > 초딩도 쉽게하는 라라벨',
      '경제 > 초딩도 쉽게 이해하는 경제',
      '개발 > 라라벨 기초',
    ];

    $tagSets = [
      ['#국내여행', '#기차여행', '#당일치기', '#뚜벅이코스', '#여행일정', '#여행기록', '#여행코스', '#주말여행', '#힐링여행', '#국내명소', '#교통팁', '#예산관리', '#사진스팟', '#혼자여행', '#커플여행', '#가족여행', '#여행준비', '#숙소추천', '#맛집코스', '#여행후기'],
      ['#국내맛집', '#카페투어', '#로컬맛집', '#디저트추천', '#브런치맛집', '#점심추천', '#저녁추천', '#가성비맛집', '#핫플맛집', '#카페추천', '#베이커리', '#커피맛집', '#주말맛집', '#데이트코스', '#푸드리뷰', '#맛집기록', '#메뉴추천', '#줄서기팁', '#동네맛집', '#먹방코스'],
      ['#라라벨', '#Laravel', '#PHP', '#입문개발', '#백엔드개발', '#웹개발', '#MVC', '#라우팅', '#컨트롤러', '#블레이드', '#Eloquent', '#마이그레이션', '#시드데이터', '#폼처리', '#인증구현', '#API개발', '#테스트코드', '#개발공부', '#초보개발자', '#코딩기초'],
      ['#경제기초', '#용어정리', '#초보경제', '#생활경제', '#재테크기초', '#금융상식', '#가계부관리', '#소비습관', '#저축습관', '#투자기초', '#물가이해', '#금리이해', '#대출상식', '#부채관리', '#현금흐름', '#경제뉴스', '#정책이해', '#경제공부', '#초보재테크', '#돈관리'],
      ['#라라벨기초', '#웹개발', '#백엔드', '#실습정리', '#프로젝트구성', '#개발환경', '#코드리팩터링', '#예외처리', '#DB설계', '#쿼리최적화', '#뷰템플릿', '#유효성검사', '#파일업로드', '#배포기초', '#버전관리', '#깃사용법', '#개발노트', '#초급강의', '#실전코딩', '#기초강좌'],
    ];

    $titleBase = $baseTitles[($idx - 1) % count($baseTitles)];
    $round = intdiv(max($idx - 1, 0), count($baseTitles)) + 1;
    $title = $titleBase . ' ' . $round;
    $category = $categories[($idx - 1) % count($categories)];
    $tags = $tagSets[($idx - 1) % count($tagSets)];

    $baseTime = strtotime('2026-01-01 08:30:00');
    $time = $baseTime + ($idx * 3701);
    if ($idx % 3 === 0) {
      $time += 7 * 86400;
    }
    $displayDate = date('Y-m-d H:i:s', $time);
    $visibility = $idx % 2 === 0 ? '공개' : '비공개';

    $articleBlocks = [
      '한 달 생계비 250만원을 보호하는 \'생계비 계좌\'가 2026년 2월부터 본격적으로 운영되고 있습니다. 이 제도는 민사집행법 개정에 따라 도입됐고, 금융권 전반에서 상품이 출시되며 실제 이용 단계에 들어갔습니다.',
      '핵심은 계좌 안의 일정 금액을 압류 대상에서 제외한다는 점입니다. 개인은 전 금융권 통합 기준으로 1인 1계좌만 개설할 수 있고, 계좌 잔액 및 월 입금 한도는 250만원으로 제한됩니다. 보호 한도를 넘는 금액은 예비 계좌로 자동 이체되도록 설계됐습니다.',
      '기존 제도에서도 최저생계비 일부는 압류 금지였지만, 실제 현장에서는 채무자가 매달 법원에 해제 신청을 반복해야 하는 불편이 컸습니다. 신청을 놓치면 생활비까지 묶이는 사례가 발생했고, 임차료나 공과금 같은 기본 지출에 즉시 문제가 생긴다는 지적이 이어졌습니다.',
      '과거 압류방지 통장은 주로 복지수급자 등 특정 계층 중심으로 좁게 운영된 반면, 이번 생계비 계좌는 적용 범위를 넓혀 실질적인 경제활동 유지에 초점을 맞췄다는 평가가 나옵니다. 통장이 막혀 임금 수령 자체가 어려워지는 악순환을 줄이려는 취지입니다.',
      '정책상품 성격에 맞춰 수수료 면제 혜택도 함께 제공됩니다. 은행별로 타행이체나 ATM 출금 수수료 감면 조건이 있고, 일부 은행은 모바일 앱을 통한 간편 개설을 지원합니다. 이용 접근성을 높여 제도 도입 효과를 빠르게 체감하도록 한 구성입니다.',
      '요약하면, 이번 제도는 채무 상태에서도 최소한의 생활자금을 안정적으로 운용할 수 있도록 안전장치를 제도화한 것입니다. 압류 절차로 인한 생계 불안을 낮추고, 노동소득 수령과 일상 금융활동을 유지하게 만드는 데 정책 목적이 있습니다.',
    ];

    $contentBlocks = [];
    for ($i = 0; $i < 9; $i++) {
      foreach ($articleBlocks as $block) {
        $contentBlocks[] = $block;
      }
    }
  @endphp

  <section class="col-12 col-lg-8 mx-auto blog-page-scope">
    <div class="board-card blog-show-page p-3 p-lg-4 rounded-3 shadow-sm">
      <h1 class="blog-show-title">{{ $title }}</h1>
      <div class="blog-show-meta">
        <span class="blog-show-meta-category">{{ $category }}</span>
        <span class="blog-show-meta-date">{{ $displayDate }}</span>
      </div>
      <div class="blog-show-visibility">
        공개여부:
        <span class="blog-show-visibility-badge {{ $visibility === '공개' ? 'is-public' : '' }}">{{ $visibility }}</span>
      </div>

      <article class="blog-show-content">
        @foreach ($contentBlocks as $block)
          <p>{{ $block }}</p>
        @endforeach
      </article>

      <ul class="blog-show-tags">
        @foreach ($tags as $tag)
          <li>{{ $tag }}</li>
        @endforeach
      </ul>

      <div class="blog-show-actions">
        <a href="{{ route('blogs.edit', ['slug' => $slug, 'idx' => $idx]) }}" class="blog-show-action-btn btn btn-outline-secondary">수정</a>
        <button type="button" class="blog-show-action-btn btn btn-outline-danger" id="blogShowDeleteBtn">삭제</button>
        <button type="button" class="blog-show-action-btn btn btn-outline-primary" id="blogShowPublicBtn">공개설정</button>
        <a href="{{ route('blogs.index', ['slug' => $slug]) }}" class="blog-show-action-btn btn btn-secondary">목록</a>
      </div>
    </div>
  </section>
@endsection

@section('script')
  <script>
    (function ($) {
      if (!$) {
        return;
      }

      $('#blogShowDeleteBtn').on('click', function () {
        window.alert('삭제 기능은 다음 단계에서 연결 예정입니다.');
      });

      $('#blogShowPublicBtn').on('click', function () {
        window.alert('공개설정 기능은 다음 단계에서 연결 예정입니다.');
      });
    })(window.jQuery);
  </script>
@endsection
