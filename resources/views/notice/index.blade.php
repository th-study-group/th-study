@extends('layouts.app')

@section('title', '공지사항')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="board-head-text">
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0 board-ellipsis-mobile">{{ config('app.name') }} 공지사항 전달헤드려요</p>
                </div>
            </div>

            <div class="board-table-wrap mt-3">
                <table id="notice_table" class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 20px;">
                        <col style="width: 140px;">
                        <col style="width: 50px;" class="board-col-hidden">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">No</th>
                            <th scope="col" class="text-nowrap">제목</th>
                            <th scope="col" class="text-nowrap board-col-hidden">등록일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="notice_empty_row_cell" class="text-center text-secondary py-4" colspan="1">등록된 공지 내역이 없습니다.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="공지사항 페이지네이션">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><span class="page-link">이전</span></li>
                    <li class="page-item active"><span class="page-link">1</span></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">다음</a></li>
                </ul>
            </nav>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            updateEmptyRowColspan('#notice_table', '#notice_empty_row_cell');
            $(window).on('resize', function(){
                updateEmptyRowColspan('#notice_table', '#notice_empty_row_cell');
            });
        });
    </script>
@endsection
