@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                <div>
                    <h2 class="board-title h5 mb-1">공지사항</h2>
                    <p class="text-secondary small mb-0">게시물의 상세정보를 확인하세요</p>
                </div>
            </div>

            <div class="mt-3">
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">제목</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->title }}</div>
                </div>
                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">작성자</span>
                    <div class="board-field bg-light rounded-3 px-3 py-2">{{ $post->user?->nick_name ?? $post->user?->name ?? '-' }}</div>
                </div>

                <div class="notice-show-ad notice-show-display-ad">
                    <x-adsense
                        class="notice-show-ad-slot"
                        :ad-slot="$adsenseNoticeShowTopDisplayAdSlot"
                        :format="$adsenseNoticeShowTopDisplayFormat"
                        :full-width-responsive="$adsenseNoticeShowTopDisplayFullWidthResponsive" />
                </div>

                {{-- 카카오 애드핏 --}}
                <div class="text-center my-3 d-block d-md-none">
                    <x-adfit
                        :unit="config('adfit.mobile.slim.unit')"
                        :width="config('adfit.mobile.slim.width')"
                        :height="config('adfit.mobile.slim.height')" />
                </div>

                <div class="text-center my-3 d-none d-md-block">
                    <x-adfit
                        :unit="config('adfit.pc.slim.unit')"
                        :width="config('adfit.pc.slim.width')"
                        :height="config('adfit.pc.slim.height')" />
                </div>
                {{-- 카카오 애드핏 --}}

                <div class="notice-show-ad notice-show-in-article-ad">
                    <x-adsense
                        class="notice-show-ad-slot"
                        :ad-slot="$adsenseNoticeShowContentInArticleAdSlot"
                        :format="$adsenseNoticeShowContentInArticleFormat"
                        :data-ad-layout="$adsenseNoticeShowContentInArticleLayout" />
                </div>

                <div class="mb-3">
                    <span class="form-label small text-secondary d-block mb-1">내용</span>
                    <div class="board-field board-content bg-light rounded-3 px-3 py-2">
                        <div class="board-content-text">{{ $post->content }}</div>
                    </div>
                </div>
                <div class="board-meta text-secondary small">
                    등록시각: {{ $post->create_datetime }}
                </div>
            </div>
        </div>

        <div class="notice-show-ad notice-show-bottom-multiplex-ad">
            <x-adsense
                class="notice-show-ad-slot"
                :ad-slot="$adsenseNoticeShowBottomMultiplexAdSlot"
                :format="$adsenseNoticeShowBottomMultiplexFormat" />
        </div>
        <div class="notice-show-bottom-ad-tail" aria-hidden="true"></div>

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('posts.index', ['post_type' => 'notice']) }}" class="btn btn-secondary">목록</a>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(function () {
            window.requestAnimationFrame(function () {
                document.querySelectorAll('.notice-show-ad .adsbygoogle').forEach(function (ad) {
                    if (ad.dataset.adsensePushQueued === 'true' || ad.dataset.adsbygoogleStatus || ad.offsetWidth <= 0) {
                        return;
                    }

                    ad.dataset.adsensePushQueued = 'true';

                    try {
                        (window.adsbygoogle = window.adsbygoogle || []).push({});
                    } catch (error) {
                        console.warn('AdSense notice ad initialization failed.', error);
                    }
                });
            });
        });
    </script>
@endsection
