<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <meta name="description" content="티에이치스터디그룹은 성장하는 개발자가 기록과 실험을 통해 실력을 확장하고, 서비스 운영과 수익화를 경험하는 개발자 성장 플랫폼입니다.">
        <meta property="og:title" content="티에이치스터디그룹 - 개발자 성장 플랫폼">
        <meta property="og:description" content="개발자의 기록, 실험, 운영, 수익화를 기반으로 성장하는 플랫폼">
        <meta property="og:image" content="{{ asset('images/main_logo.png') }}">
        <meta property="og:type" content="website">

        {{-- Favicon :: WEB --}}
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/th_favicon_16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/th_favicon_32.png') }}">
        <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/th_favicon_48.png') }}">

        {{-- Favicon :: Apple Touch --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/th_favicon_ios_108.png') }}">

        {{-- Favicon :: Android / PWA Icons --}}
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/th_favicon_and_256.png') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/th_favicon_and_512.png') }}">

        {{-- PWA --}}
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

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

        @include('layouts.back-to-top')
        
        {{-- 스크립트 단일 페이지 --}}
        @yield('script')
    </body>
</html>
