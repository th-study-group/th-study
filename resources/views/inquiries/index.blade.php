@extends('layouts.app')

@section('title', '문의내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="d-flex align-items-start justify-content-between gap-2 board-head-top">
                    <div class="flex-grow-1 board-min-w-0 board-head-text">
                        <h2 class="board-title h5 mb-1">나의문의내역</h2>
                        <p class="text-secondary small mb-0 board-ellipsis-mobile">현재까지 등록하신 문의사항을 조회 할 수 있습니다.</p>
                    </div>
                    <a href="{{ route('inquiries.create') }}" class="btn btn-dark btn-sm text-nowrap">문의하기</a>
                </div>

                <div class="board-filter-box">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <h3 class="board-filter-title small text-secondary mb-0">검색조건 설정</h3>
                        <button class="board-filter-toggle btn btn-outline-secondary btn-sm collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#inquiryFilters"
                                aria-expanded="false"
                                aria-controls="inquiryFilters">
                            접기
                        </button>
                    </div>

                    <div id="inquiryFilters" class="collapse show mt-2">
                        <form id="form_search" name="form_search" method="GET" action="{{ route('inquiries.index') }}">
                            @csrf
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-4">
                                    <label for="search_start_date" class="form-label small text-secondary mb-1">기간</label>
                                    <div class="board-date-range">
                                        <input type="text"
                                               id="search_start_date" 
                                               name="search_start_date" 
                                               class="form-control form-control-sm">
                                        <span class="text-secondary small">~</span>
                                        <input type="text"
                                               id="search_end_date"
                                               name="search_end_date" 
                                               class="form-control form-control-sm">
                                    </div>
                                </div>

                            </div>
                            <div class="d-grid mt-3">
                                <button type="button" id="btn_search" class="btn btn-primary">검색</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="board-table-wrap mt-3">
                <table id="inquiry_table" class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 35px;">
                        <col style="width: 45px;">
                        <col style="width: 220px;">
                        <col class="board-col-hidden" style="width: 80px;">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">번호</th>
                            <th scope="col" class="text-nowrap">상태</th>
                            <th scope="col" class="text-nowrap">제목</th>
                            <th scope="col" class="board-col-hidden text-nowrap">등록일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="empty_row_cell" class="text-center text-secondary py-4" colspan="1">등록된 글이 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('inquiries.create') }}" class="btn btn-dark btn-sm text-nowrap">문의하기</a>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="문의내역 페이지네이션">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><span class="page-link">이전</span></li>
                    <li class="page-item active"><span class="page-link">1</span></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">다음</a></li>
                </ul>
            </nav>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            var today = new Date();
            var oneYearAgo = new Date();
            oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);

            initBirthDatePicker('#search_start_date', {
                defaultDate: oneYearAgo,
                maxDate: today
            });
            initBirthDatePicker('#search_end_date', {
                defaultDate: today,
                maxDate: today
            });

            $("#btn_search").on("click", function() {
                alert('ok!!');
            });

            $(window).on('resize', function(){
                updateEmptyRowColspan('#inquiry_table', '#empty_row_cell');
            });

            updateEmptyRowColspan('#inquiry_table', '#empty_row_cell');
        });
    </script>
@endsection
