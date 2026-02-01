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
                        <form id="form_search" name="form_search" method="GET" action="{{ route('admins.members.index') }}">
                            @csrf
                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-4">
                                    <label for="search_name" class="form-label small text-secondary mb-1">이름</label>
                                    <input type="text"
                                           id="search_name"
                                           name="seach_name"
                                           class="form-control form-control-sm"
                                           placeholder="이름">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_nickname" class="form-label small text-secondary mb-1">닉네임</label>
                                    <input type="text"
                                           id="search_nickname"
                                           name="search_nickname"
                                           class="form-control form-control-sm"
                                           placeholder="닉네임">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_gender" class="form-label small text-secondary mb-1">성별</label>
                                    <select id="search_gender" name="search_gender" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        <option value="M">남</option>
                                        <option value="W">여</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_marketing" class="form-label small text-secondary mb-1">마케팅동의여부</label>
                                    <select id="search_marketing" name="search_marketing" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        <option value="Y">동의</option>
                                        <option value="N">미동의</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_grade" class="form-label small text-secondary mb-1">회원등급</label>
                                    <select id="search_grade" name="search_grade" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        <option value="general">일반</option>
                                        <option value="admin">관리자</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="search_status" class="form-label small text-secondary mb-1">회원상태</label>
                                    <select id="search_status" name="search_status" class="form-select form-select-sm">
                                        <option value="">전체</option>
                                        <option value="email_pending">메일인증대기</option>
                                        <option value="password_reset">비밀번호재설정</option>
                                    </select>
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
                <table id="member_table" class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 45px;">
                        <col style="width: 110px;">
                        <col style="width: 110px;">
                        <col style="width: 250px;">
                        <col style="width: 170px;">
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
                        <tr>
                            <td id="empty_row_cell" class="text-center text-secondary py-4" colspan="9">등록된 글이 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
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

            $("#btn_search").on("click", function() {
                alert('ok!!');
            });

            $(window).on('resize', function(){
                updateEmptyRowColspan('#member_table', '#empty_row_cell');
            });

            updateEmptyRowColspan('#member_table', '#empty_row_cell');
        });
    </script>
@endsection
