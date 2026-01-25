@extends('layouts.app')

@section('title', '정보관리')

@section('content')
  <main class="col-lg-10 content-col">
    <section class="bg-white p-4 p-lg-5 rounded-3 shadow-sm">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
          <h1 class="h3 fw-semibold mb-1">비디오</h1>
          <p class="text-secondary mb-0">카테고리별 콘텐츠를 한눈에 확인하세요.</p>
        </div>
        <span class="badge text-bg-dark">전체</span>
      </div>

      <div class="row g-3">
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">개발</h6>
            <p class="text-secondary mb-0">백엔드, 프레임워크, API 튜토리얼</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">여행</h6>
            <p class="text-secondary mb-0">도시별 브이로그와 로컬 가이드</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">노래</h6>
            <p class="text-secondary mb-0">라이브, 커버, 플레이리스트</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">추천</h6>
            <p class="text-secondary mb-0">최근 인기 영상 모아보기</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">업데이트</h6>
            <p class="text-secondary mb-0">이번 주 새로 추가된 콘텐츠</p>
          </div>
        </div>
        <div class="col-md-6 col-xl-4">
          <div class="p-3 border rounded-3 h-100">
            <h6 class="fw-semibold mb-2">아카이브</h6>
            <p class="text-secondary mb-0">장르별 전체 영상 목록</p>
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
