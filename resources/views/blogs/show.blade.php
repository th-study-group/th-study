@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $metaKeywords)
@section('og_title', $metaTitle)
@section('og_description', $metaDescription)
@section('og_image', $metaImage)
@section('og_image_width', $metaImageWidth)
@section('og_image_height', $metaImageHeight)
@section('og_url', $metaUrl)
@section('og_type', $metaType)
@section('canonical_url', $canonicalUrl)

@push('styles')
  <link href="{{ $blogCssUrl }}" rel="stylesheet" />
@endpush

@section('content')
  <section class="col-12 col-lg-8 mx-auto blog-page-scope">
    <div class="board-card blog-show-page p-3 p-lg-4 rounded-3 shadow-sm">
      <div class="blog-show-head">
        <div class="blog-show-head-category">{{ $note->group_topic_name }}</div>
        <button type="button" class="btn_note_list btn btn-dark btn-sm blog-show-top-list">목록</button>
      </div>
      <h1 class="blog-show-title">{{ $note->subject }}</h1>

      <div class="blog-show-meta">
        <span class="blog-show-meta-date">{{ $displayCreateDatetime }}</span>
      </div>

      @if ($canManageVisibility)
        <div class="blog-show-visibility">
          <span class="blog-show-visibility-badge {{ $visibilityClass }}">{{ $useFlagLabel }}</span>
        </div>
      @endif

      <div class="blog-show-ad blog-show-display-ad">
        <x-adsense
          class="blog-show-display-ad-slot"
          :ad-slot="$adsenseNotePageTopDisplayAdSlot"
          :format="$adsenseNotePageTopDisplayFormat"
          :full-width-responsive="$adsenseNotePageTopDisplayFullWidthResponsive" />
      </div>

      <article class="blog-show-content">{!! $contentHtml !!}</article>

      <div class="blog-show-ad blog-show-in-article-ad">
        <x-adsense
          class="blog-show-in-article-ad-slot"
          :ad-slot="$adsenseNotePageContentInArticleAdSlot"
          :format="$adsenseNotePageContentInArticleFormat"
          :data-ad-layout="$adsenseNotePageContentInArticleLayout" />
      </div>

      <section class="blog-show-related" aria-label="관련 글 목록">
        <h2 class="blog-show-related-title">
          <span class="blog-show-related-topic">{{ $topicName }}</span>
          <span>관련 글</span>
        </h2>
        <ul class="blog-show-related-list">
          @forelse($relatedNotes as $related)
            <li class="blog-show-related-item">
              <a href="{{ $related['show_url'] }}" class="blog-show-related-subject">{{ $related['subject'] }}</a>
              <span class="blog-show-related-date">{{ $related['relative_time'] }}</span>
            </li>
          @empty
            <li class="blog-show-related-item">
              <span class="blog-show-related-subject">관련 글이 없습니다.</span>
              <span class="blog-show-related-date">-</span>
            </li>
          @endforelse
        </ul>
      </section>

      <div class="blog-show-ad blog-show-adfit-ad d-none d-md-block">
        <x-adfit
          :unit="$adfitPcRectangleUnit"
          :width="$adfitPcRectangleWidth"
          :height="$adfitPcRectangleHeight" />
      </div>

      <div class="blog-show-ad blog-show-adfit-ad d-block d-md-none">
        <x-adfit
          :unit="$adfitMobileRectangleUnit"
          :width="$adfitMobileRectangleWidth"
          :height="$adfitMobileRectangleHeight" />
      </div>

      @if ($hasTags)
        <ul class="blog-show-tags">
          @foreach ($tagNames as $tagName)
            <li>#{{ $tagName }}</li>
          @endforeach
        </ul>

        <div class="blog-show-ad blog-show-bottom-multiplex-ad">
          <x-adsense
            class="blog-show-bottom-multiplex-ad-slot"
            :ad-slot="$adsenseNotePageBottomMultiplexAdSlot"
            :format="$adsenseNotePageBottomMultiplexFormat" />
        </div>
      @endif

      <div class="blog-show-actions">
        @can('update', $note)
          <button type="button" id="btn_note_modify" class="btn btn-outline-secondary">수정</button>
        @endcan
        @if ($canDeleteByVisibility)
          @can('delete', $note)
            <button type="button" id="btn_note_delete" class="btn btn-outline-danger">삭제</button>
          @endcan
        @endif
        @can('updateUseFlag', $note)
          <button type="button" id="btn_note_use_flag" class="btn btn-outline-primary">공개설정</button>
        @endcan
        <button type="button" class="btn_note_list btn btn-dark">목록</button>
      </div>
    </div>
    <button
      type="button"
      id="btn_share_copy"
      class="blog-show-share-fab"
      aria-label="현재 주소 복사">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M10.6 13.4l2.8-2.8"
              stroke="currentColor"
              stroke-width="2.1"
              stroke-linecap="round"
              stroke-linejoin="round"/>
        <path d="M8.1 14.2l-1.4 1.4a3 3 0 1 1-4.2-4.2l3-3a3 3 0 0 1 4.2 0"
              stroke="currentColor"
              stroke-width="2.1"
              stroke-linecap="round"
              stroke-linejoin="round"/>
        <path d="M15.9 9.8l1.4-1.4a3 3 0 1 1 4.2 4.2l-3 3a3 3 0 0 1-4.2 0"
              stroke="currentColor"
              stroke-width="2.1"
              stroke-linecap="round"
              stroke-linejoin="round"/>
      </svg>
    </button>
  </section>
@endsection

@push('scripts')
  <script src="{{ $blogJsUrl }}" defer></script>
@endpush

@section('script')
  <script>
    $(function() {

      function hideUnfilledBottomMultiplexAd(ad) {
        var wrapper = ad.closest('.blog-show-bottom-multiplex-ad');
        if (!wrapper) {
          return;
        }

        var reportStatus = function () {
          var adStatus = ad.dataset.adStatus || '';
          if (adStatus === 'unfilled' || adStatus === 'unfill-optimized') {
            wrapper.hidden = true;
          }
          return adStatus;
        };

        var observer = new MutationObserver(function () {
          var adStatus = reportStatus();
          if (adStatus === 'filled' || adStatus === 'unfilled' || adStatus === 'unfill-optimized') {
            observer.disconnect();
            window.clearTimeout(timeoutId);
          }
        });
        var timeoutId = window.setTimeout(function () {
          observer.disconnect();
          reportStatus();
        }, 10000);

        observer.observe(ad, {
          attributes: true,
          attributeFilter: ['data-ad-status', 'data-adsbygoogle-status'],
        });
        reportStatus();
      }

      window.requestAnimationFrame(function () {
        document.querySelectorAll('.blog-show-ad .adsbygoogle').forEach(function (ad) {
          if (ad.dataset.adsensePushQueued === 'true' || ad.dataset.adsbygoogleStatus) {
            return;
          }

          var attempts = 0;
          var maxAttempts = 30;

          function pushWhenWidthIsReady() {
            if (ad.dataset.adsensePushQueued === 'true' || ad.dataset.adsbygoogleStatus) {
              return;
            }

            if (ad.getBoundingClientRect().width <= 0) {
              attempts += 1;
              if (attempts < maxAttempts) {
                window.requestAnimationFrame(pushWhenWidthIsReady);
              }
              return;
            }

            ad.dataset.adsensePushQueued = 'true';

            try {
              (window.adsbygoogle = window.adsbygoogle || []).push({});
              if (ad.matches('.blog-show-bottom-multiplex-ad .adsbygoogle')) {
                hideUnfilledBottomMultiplexAd(ad);
              }
            } catch (error) {
              console.warn('AdSense 광고 초기화에 실패했습니다.', error);
            }
          }

          pushWhenWidthIsReady();
        });
      });

      const listUrl = "{{ $listUrl }}";
      const editUrl = "{{ $editUrl }}";
      const deleteUrl = "{{ $deleteUrl }}";
      const useFlagUrl = "{{ $useFlagUrl }}";
      const useFlag = "{{ $useFlag }}";

      initBlogDetailContentEnhancements();

      initShareCopyButtons('#btn_share_copy', {
        getUrl: function () {
          return window.location.href;
        }
      });

      $(".btn_note_list").on("click", function() {
        location.href = listUrl;
      });

      $("#btn_note_modify").on("click", function() {
        if (!confirm('수정하시겠습니까?')) {
          return;
        }
        location.href = editUrl;
      });

      $('#btn_note_delete').on('click', function(){
        if (!confirm('삭제하시겠습니까?')) {
            return;
        }
        requestAjax({
            method: 'DELETE',
            url: deleteUrl,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ $csrfToken }}',
            },
            onSuccess: function () {
                alert('노트가 삭제되었습니다.');
                location.href = listUrl;
            },
            onError: function (xhr) {
                let message = '삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    //message = xhr.responseJSON.message;
                }
                alert(message);
            },
        });
      });

      $('#btn_note_use_flag').on('click', function(){
        const message = useFlag === 'Y'
            ? '이미 공개중입니다. 비공개로 하시겠습니까?'
            : '현재 비공개입니다. 공개로 하시겠습니까?';

        if (!confirm(message)) {
            return;
        }

        requestAjax({
          method: 'PATCH',
          url: useFlagUrl,
          dataType: 'json',
          headers: {
              'X-CSRF-TOKEN': '{{ $csrfToken }}',
          },
          onSuccess: function () {
              alert('공개 여부가 변경되었습니다.');
              location.reload();
          },
          onError: function (xhr) {
              let message = '공개여부 변경 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
              if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                  //message = xhr.responseJSON.message;
              }
              alert(message);
          },
        });
      });
    });
  </script>
@endsection
