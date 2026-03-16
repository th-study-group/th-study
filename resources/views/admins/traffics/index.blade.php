@extends('layouts.app')

@section('title', '일일 유입 현황')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="d-flex align-items-start justify-content-between gap-2 board-head-top">
                    <div class="flex-grow-1 board-min-w-0 board-head-text">
                        <h2 class="board-title h5 mb-1">일일 유입 현황</h2>
                        <p class="text-secondary small mb-0 board-ellipsis-mobile">날짜 기준 유입 로그를 조회합니다.</p>
                    </div>
                </div>

                <div class="board-filter-box">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h3 class="board-filter-title small text-secondary mb-0">검색조건 설정</h3>
                        <button class="board-filter-toggle btn btn-outline-secondary btn-sm collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#trafficFilters"
                                aria-expanded="false"
                                aria-controls="trafficFilters">
                            접기
                        </button>
                    </div>

                    <div id="trafficFilters" class="collapse show mt-2">
                        @if ($errors->any())
                            <div class="alert alert-warning d-flex align-items-center gap-2 small mb-3" role="alert">
                                <span class="badge text-bg-warning text-dark">경고</span>
                                <span>검색 조건을 확인해 주세요.</span>
                            </div>
                        @endif

                        <form id="form_search" name="form_search" method="GET" action="{{ route('admins.traffics.index') }}">
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-4">
                                    <label for="search_date" class="form-label small text-secondary mb-1">날짜</label>
                                    <input type="date"
                                           id="search_date"
                                           name="search_date"
                                           required
                                           max="{{ now()->toDateString() }}"
                                           class="form-control form-control-sm"
                                           value="{{ old('search_date', $filters['search_date'] ?? now()->toDateString()) }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_device" class="form-label small text-secondary mb-1">디바이스</label>
                                    <select id="search_device" name="search_device" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        @foreach ($deviceOptions as $deviceValue => $deviceLabel)
                                            <option value="{{ $deviceValue }}" @selected(old('search_device', $filters['search_device'] ?? '') === $deviceValue)>{{ $deviceLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_ip" class="form-label small text-secondary mb-1">아이피</label>
                                    <input type="text"
                                           id="search_ip"
                                           name="search_ip"
                                           class="form-control form-control-sm"
                                           placeholder="아이피"
                                           value="{{ old('search_ip', $filters['search_ip'] ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <span class="form-label small text-secondary d-block mb-1">접근시각 정렬</span>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="search_order"
                                                   id="search_order_desc"
                                                   value="desc"
                                                   @checked(old('search_order', $filters['search_order'] ?? 'desc') === 'desc')>
                                            <label class="form-check-label small" for="search_order_desc">내림차순</label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="search_order"
                                                   id="search_order_asc"
                                                   value="asc"
                                                   @checked(old('search_order', $filters['search_order'] ?? 'desc') === 'asc')>
                                            <label class="form-check-label small" for="search_order_asc">오름차순</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-3">
                                <button type="submit" id="btn_search" class="btn btn-primary">검색</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="board-table-wrap mt-3">
                <table id="traffic_table" class="table table-bordered table-hover align-middle mb-0 board-table board-table-break">
                    <colgroup>
                        <col style="width: 60px;">
                        <col style="width: 180px;">
                        <col style="width: 260px;">
                        <col style="width: 100px;">
                        <col style="width: 120px;">
                        <col style="width: 160px;">
                        <col style="width: 120px;">
                        <col style="width: 166px;">
                        <col style="width: 199px;">
                        <col style="width: 280px;">
                        <col style="width: 380px;">
                        <col style="width: 220px;">
                        <col style="width: 220px;">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">No</th>
                            <th scope="col" class="text-nowrap">접근시각</th>
                            <th scope="col" class="text-nowrap">접근페이지</th>
                            <th scope="col" class="text-nowrap">디바이스</th>
                            <th scope="col" class="text-nowrap">기기 제조사</th>
                            <th scope="col" class="text-nowrap">기기 모델명</th>
                            <th scope="col" class="text-nowrap">OS정보</th>
                            <th scope="col" class="text-nowrap">접속브라우저</th>
                            <th scope="col" class="text-nowrap">아이피</th>
                            <th scope="col" class="text-nowrap">유입경로</th>
                            <th scope="col" class="text-nowrap">User Agent</th>
                            <th scope="col" class="text-nowrap">세션ID</th>
                            <th scope="col" class="text-nowrap">회원아이디</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $number = $logs->total() - (($logs->currentPage() - 1) * $logs->perPage()) - $loop->index;
                            @endphp
                            <tr class="text-center">
                                <td class="text-nowrap">{{ $number }}</td>
                                <td class="text-nowrap">{{ $log->access_datetime?->format('Y-m-d H:i:s') ?? '-' }}</td>
                                <td class="text-start">
                                    @if (!empty($log->access_page_href))
                                        <a href="{{ $log->access_page_href }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="text-decoration-none text-dark">
                                            {{ $log->access_page ?? '-' }}
                                            <span class="ms-1 text-secondary" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16" fill="none">
                                                    <path d="M10 2H14V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 2L8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14 9V12.5C14 13.3284 13.3284 14 12.5 14H3.5C2.67157 14 2 13.3284 2 12.5V3.5C2 2.67157 2.67157 2 3.5 2H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                        </a>
                                    @else
                                        {{ $log->access_page ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-nowrap">{{ $log->device_label ?? '-' }}</td>
                                <td class="text-nowrap">{{ $log->device_brand ?? '-' }}</td>
                                <td class="text-nowrap">{{ $log->device_model ?? '-' }}</td>
                                <td class="text-nowrap">{{ $log->os ?? '-' }}</td>
                                <td class="text-nowrap">{{ $log->browser ?? '-' }}</td>
                                <td class="text-start">{{ $log->ip ?? '-' }}</td>
                                <td class="text-start">{{ $log->referer_url ?? $log->referer_host ?? '-' }}</td>
                                <td class="text-start">{{ $log->user_agent ?? '-' }}</td>
                                <td class="text-start">{{ $log->session_id ?? '-' }}</td>
                                <td class="text-start">{{ $log->user?->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td id="empty_row_cell" class="text-center text-secondary py-4" colspan="13">검색된 내역이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="일일 유입 현황 페이지네이션">
                {{ $logs->links() }}
            </nav>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/board.js') }}?v={{ filemtime(public_path('js/board.js')) }}"></script>
@endpush

@section('script')
    <script>
        $(function(){
            $(window).on('resize', function(){
                updateEmptyRowColspan('#traffic_table', '#empty_row_cell');
            });

            updateEmptyRowColspan('#traffic_table', '#empty_row_cell');
        });
    </script>
@endsection
