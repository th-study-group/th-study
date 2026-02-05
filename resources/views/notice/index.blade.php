@extends('layouts.app')

@section('title', '공지사항')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div class="d-flex align-items-start justify-content-between gap-2 board-head-top">
                    <div class="flex-grow-1 board-min-w-0 board-head-text">
                        <h2 class="board-title h5 mb-1">공지사항</h2>
                        <p class="text-secondary small mb-0 board-ellipsis-mobile">{{ config('app.name') }} 공지사항 전달헤드려요</p>
                    </div>
                </div>

            </div>

            <div class="board-table-wrap mt-3">
                <table id="notice_table" class="table table-bordered table-hover align-middle mb-0 board-table">
                    <colgroup>
                        <col style="width: 45px;">
                        <col style="width: 220px;">
                        <col style="width: 50px;" class="board-col-hidden">
                    </colgroup>
                    <thead class="table-light">
                        <tr class="text-center">
                            <th scope="col" class="text-nowrap">번호</th>
                            <th scope="col" class="text-nowrap">제목</th>
                            <th scope="col" class="text-nowrap board-col-hidden">등록일</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr class="text-center notice-row" data-href="{{ route('posts.show', ['post_type' => 'notice', 'idx' => $post->idx]) }}" style="cursor: pointer;">
                                <td class="text-nowrap">{{ $posts->total() - (($posts->currentPage() - 1) * $posts->perPage()) - $loop->index }}</td>
                                <td class="text-start">
                                    <span class="text-decoration-none board-ellipsis d-block">
                                        {{ $post->title }}
                                    </span>
                                </td>
                                <td class="text-nowrap board-col-hidden">{{ $post->create_datetime?->diffForHumans() ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td id="notice_empty_row_cell" class="text-center text-secondary py-4" colspan="3">등록된 공지 내역이 없습니다.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="board-pagination d-flex justify-content-center mt-4" aria-label="공지사항 페이지네이션">
                {{ $posts->links() }}
            </nav>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function(){
            $('.notice-row').on('click', function(e){
                if ($(e.target).closest('a, button, input, select, textarea').length) {
                    return;
                }

                const href = $(this).data('href');
                if (href) {
                    location.href = href;
                }
            });

            updateEmptyRowColspan('#notice_table', '#notice_empty_row_cell');
            $(window).on('resize', function(){
                updateEmptyRowColspan('#notice_table', '#notice_empty_row_cell');
            });
        });
    </script>
@endsection
