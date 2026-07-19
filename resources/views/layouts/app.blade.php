<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

        {{-- 네이버 검색엔진 SEO 서치어드바이저 --}}
        <meta name="naver-site-verification" content="2a350b8a4be67f3a443b1eafc451fe588a8ee0b5" />

        {{-- 구글 애드센스 --}}
        @if (config('services.adsense.id'))
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('services.adsense.id') }}" crossorigin="anonymous"></script>
        @endif

        {{-- 구글 애널리틱스 ga4 --}}
        @if($analyticsEnabled ?? false)
            <script
                async
                src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.measurement_id') }}">
            </script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ config('analytics.measurement_id') }}');
        </script>
        @endif

        {{-- 아이폰 주소, 날짜, 이메일 등 밑줄 방지 --}}
        <meta name="format-detection" content="telephone=no, date=no, email=no, address=no">

        {{-- PWA 설정 --}}
        <meta name="apple-mobile-web-app-capable" content="yes"> {{-- iOS standalone --}}
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"> {{-- 상태바 --}}

        {{-- og image --}}
        <meta name="description" content="@yield('meta_description', config('app.name') . '은 성장하는 개발자가 기록과 실험을 통해 실력을 확장하고, 서비스 운영과 수익화를 경험하는 개발자 성장 플랫폼입니다.')">
        <meta name="keywords" content="@yield('meta_keywords', '티에이치스터디,성장플랫폼,개인성장플랫폼,개발자료,개발블로그,개발공부,국내여행추천,여행추천,맛집추천,카페추천,국내여행블로그,숙소추천,혼자여행,여행정보')">
        
        <meta property="og:title" content="@yield('og_title', config('app.name') . ' 개발자 성장 블로그')">
        <meta property="og:description" content="@yield('og_description', '개발자의 기록, 학습, 운영, 수익화를 기반으로 성장하는 블로그')">
        <meta property="og:image" content="@yield('og_image', asset('images/og/001.png'))">
        <meta property="og:image:width" content="@yield('og_image_width', '1200')">
        <meta property="og:image:height" content="@yield('og_image_height', '630')">
        <meta property="og:url" content="@yield('og_url', url()->current())">
        <meta property="og:type" content="@yield('og_type', 'article')">

        {{-- Favicon :: WEB --}}
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/th_favicon_16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/th_favicon_32.png') }}">
        <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon/th_favicon_48.png') }}">

        {{-- Favicon :: Apple Touch --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/th_favicon_ios_180.png') }}">

        {{-- Favicon :: Android / PWA Icons --}}
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon/th_favicon_and_192.png') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon/th_favicon_and_512.png') }}">

        {{-- PWA --}}
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        {{-- PWA 푸시 정보 --}}
        <script>
            window.VAPID_PUBLIC_KEY = "{{ config('services.webpush.vapid_public_key') }}";
            window.IS_LOGGED_IN = {{ auth()->check() ? 'true' : 'false' }};
            window.CSRF_TOKEN = "{{ csrf_token() }}";
            window.JUST_LOGGED_OUT = {{ session('just_logged_out') ? 'true' : 'false' }};
            window.JUST_LOGGED_IN = {{ session('just_logged_in') ? 'true' : 'false' }};
        </script>

        {{-- 공통 및 외부 라이브러리 스크립트 --}}
        @include('partials.head-scripts')

        {{-- 스타일 페이지를 경로를 올려야 하는 경우 <script src=".."></script> --}}
        {{-- 자식뷰에서 @push('scripts') @endpush --}}
        @stack('scripts')
        
        {{-- 공통 및 외부 라이브러리 스타일 --}}
        @include('partials.head-styles')

        {{-- 스타일 페이지를 경로를 올려야 하는 경우 <like rel="stylesheet" href="..." /> --}}
        {{-- 자식뷰에서 @push('styles') @endpush --}}
        @stack('styles')

        {{--스타일 단일 페이지 --}}
        {{-- 자식뷰에서 @section('style') @endsection --}}
        @yield('style')
        
        <title>{{ config('app.name') }} :: @yield('title', '홈')</title>
    </head>
    <body>
        {{-- 레이아웃 헤더 --}}
        @include('layouts.header')

        <div class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
            <div class="row g-4">
                {{-- 레이아웃 메뉴 사이드바 --}}
                @include('layouts.menu')

                {{-- 레이아웃 노트 사이드바 --}}
                @include('layouts.note')

                {{-- 본문 컨텐츠 --}}
                @yield('content')
            </div>
        </div>

        {{-- 레이아웃 풋터 --}}
        @include('layouts.footer')

        {{-- 최상단 버튼 --}}
        @include('layouts.back-to-top')

        {{-- PWA 스플래시 --}}
        @include('layouts.splash')

        {{-- PWA 팝업 --}}
        @include('partials.pwa-popup')

        {{-- service-worker.js 등록 --}}
        <script>
            if ("serviceWorker" in navigator) { // 지원 브라우저 체크
                window.addEventListener("load", function () {
                    navigator.serviceWorker.register("/service-worker.js?v={{ filemtime(public_path('service-worker.js')) }}")
                        .then(function (reg) {
                        reg.update();
                        //console.log("SW 등록 성공:", reg.scope); // 확인용
                    })
                    .catch(function (err) {
                        //console.log("SW 등록 실패:", err);
                    });
                });
            }
        </script>

        {{-- 스크립트 단일 페이지 --}}
        @yield('script')
    </body>
</html>
