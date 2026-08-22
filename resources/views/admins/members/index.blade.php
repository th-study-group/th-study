@extends('layouts.app')

@section('title', '회원현황')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="d-flex align-items-start justify-content-between gap-2 board-head-top">
                    <div class="flex-grow-1 board-min-w-0 board-head-text">
                        <h2 class="board-title h5 mb-1">회원현황</h2>
                        <p class="text-secondary small mb-0 board-ellipsis-mobile">{{ config('app.name') }} 사이트 회원현황을 조회하고 관리 할 수 있습니다.</p>
                    </div>
                </div>

                <div class="board-filter-box">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h3 class="board-filter-title small text-secondary mb-0">검색조건 설정</h3>
                        <button class="board-filter-toggle btn btn-outline-secondary btn-sm collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#searchFilters"
                                aria-expanded="false"
                                aria-controls="searchFilters">
                            접기
                        </button>
                    </div>

                    <div id="searchFilters" class="collapse show mt-2">
                        @if ($errors->any())
                            <div class="alert alert-warning d-flex align-items-center gap-2 small mb-3" role="alert">
                                <span class="badge text-bg-warning text-dark">경고</span>
                                <span>검색 조건을 확인해 주세요.</span>
                            </div>
                        @endif
                        <form id="form_search" name="form_search" method="GET" action="{{ route('admins.members.index') }}">
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-4">
                                    <label for="search_name" class="form-label small text-secondary mb-1">이름</label>
                                    <input type="text"
                                           id="search_name"
                                           name="search_name"
                                           class="form-control form-control-sm"
                                           placeholder="이름"
                                           value="{{ old('search_name', $filters['search_name'] ?? '') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_nickname" class="form-label small text-secondary mb-1">닉네임</label>
                                    <input type="text"
                                           id="search_nickname"
                                           name="search_nickname"
                                           class="form-control form-control-sm"
                                           placeholder="닉네임"
                                           value="{{ old('search_nickname', $filters['search_nickname'] ?? '') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_gender" class="form-label small text-secondary mb-1">성별</label>
                                    <select id="search_gender" name="search_gender" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        @foreach ($sexList as $sexValue => $sexLabel)
                                            <option value="{{ $sexValue }}" @selected(old('search_gender', $filters['search_gender'] ?? '') === $sexValue)>{{ $sexLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_marketing" class="form-label small text-secondary mb-1">마케팅동의여부</label>
                                    <select id="search_marketing" name="search_marketing" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        @foreach ($terms as $termValue => $termLabel)
                                            <option value="{{ $termValue }}" @selected((string) old('search_marketing', $filters['search_marketing'] ?? '') === (string) $termValue)>{{ $termLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_grade" class="form-label small text-secondary mb-1">회원등급</label>
                                    <select id="search_grade" name="search_grade" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        @foreach ($gradeList as $gradeValue => $gradeLabel)
                                            <option value="{{ $gradeValue }}" @selected(old('search_grade', $filters['search_grade'] ?? '') === $gradeValue)>{{ $gradeLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_status" class="form-label small text-secondary mb-1">회원상태</label>
                                    <select id="search_status" name="search_status" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        <option value="email_pending" @selected(old('search_status', $filters['search_status'] ?? '') === 'email_pending')>메일인증대기</option>
                                        <option value="password_reset" @selected(old('search_status', $filters['search_status'] ?? '') === 'password_reset')>비밀번호재설정</option>
                                    </select>
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
                <table id="member_table" class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 45px;">
                        <col style="width: 110px;">
                        <col style="width: 133px;">
                        <col style="width: 280px;">
                        <col style="width: 190px;">
                        <col style="width: 90px;">
                        <col style="width: 140px;">
                        <col style="width: 90px;">
                        <col style="width: 90px;">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">No</th>
                            <th scope="col" class="text-nowrap">이름</th>
                            <th scope="col" class="text-nowrap">닉네임</th>
                            <th scope="col" class="text-nowrap">이메일</th>
                            <th scope="col" class="text-nowrap">생년월일</th>
                            <th scope="col" class="text-nowrap">성별</th>
                            <th scope="col" class="text-nowrap">마케팅동의여부</th>
                            <th scope="col" class="text-nowrap">회원등급</th>
                            <th scope="col" class="text-nowrap">인증여부</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            @php
                                $number = $members->total() - (($members->currentPage() - 1) * $members->perPage()) - $loop->index;
                                $isVerified = !empty($member->email_verify_datetime);
                            @endphp
                            <tr class="text-center inquiry-row" data-href="{{ route('admins.members.edit', ['idx' => $member->idx]) }}" role="link" tabindex="0" style="cursor: pointer;">
                                <td class="text-nowrap">{{ $number }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admins.members.edit', ['idx' => $member->idx]) }}" class="text-dark text-decoration-none board-ellipsis" title="{{ $member->name }}">{{ $member->name }}</a>
                                </td>
                                <td class="text-nowrap">{{ $member->nick_name }}</td>
                                <td class="text-nowrap text-start">{{ $member->email }}</td>
                                <td class="text-nowrap">{{ $member->birth_date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="text-nowrap">{{ $sexList[$member->sex ?? ''] ?? '-' }}</td>
                                <td class="text-nowrap">{{ $terms[(int) $member->getRawOriginal('marketing_info_agree')] ?? '-' }}</td>
                                <td class="text-nowrap">{{ $gradeList[$member->level ?? ''] ?? '-' }}</td>
                                <td class="text-nowrap">{{ $isVerified ? '인증' : '미인증' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td id="empty_row_cell" class="text-center text-secondary py-4" colspan="9">등록된 글이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="문의내역 페이지네이션">
                {{ $members->links() }}
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
            let memberNavigationStarted = false;

            window.addEventListener('pageshow', function(){
                memberNavigationStarted = false;
            });

            function navigateToMember(href) {
                if (!href || memberNavigationStarted) {
                    return;
                }

                memberNavigationStarted = true;

                if (typeof window.hideLoading === 'function') {
                    window.hideLoading({ force: true });
                }

                window.location.assign(href);
            }

            $('.inquiry-row').on('click', function(e){
                if ($(e.target).closest('a, button, input, select, textarea').length) {
                    return;
                }

                navigateToMember($(this).data('href'));
            }).on('keydown', function(e){
                if (e.key !== 'Enter' && e.key !== ' ') {
                    return;
                }

                e.preventDefault();
                navigateToMember($(this).data('href'));
            });

            $('.inquiry-row a').on('click', function(){
                if (memberNavigationStarted) {
                    return false;
                }

                memberNavigationStarted = true;

                if (typeof window.hideLoading === 'function') {
                    window.hideLoading({ force: true });
                }
            });

            $(window).on('resize', function(){
                updateEmptyRowColspan('#member_table', '#empty_row_cell');
            });

            updateEmptyRowColspan('#member_table', '#empty_row_cell');
        });
    </script>
@endsection
