@extends('layouts.app')

@section('title', '이용약관')

@section('content')
    <main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
        <section class="p-4 p-lg-5 rounded-3 shadow-sm" style="background:#f1e8dd; border:1px solid #e2d4c3;">
            <h1 class="h3 fw-bold mb-3">이용약관</h1>
            <ol class="mb-4">
            <li class="mb-3">
                <strong>제1조 (목적)</strong>
                <p class="mb-0">본 약관은 {{ config('app.name') }}이 제공하는 서비스의 이용과 관련하여 사이트와 이용자 간의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.</p>
            </li>
            <li class="mb-3">
                <strong>제2조 (서비스의 제공)</strong>
                <p class="mb-2">사이트는 이용자에게 아래와 같은 서비스(콘텐츠)를 제공합니다.</p>
                <ul class="mb-0">
                <li>개발 관련 스터디 자료 및 성장 콘텐츠</li>
                <li>맛집, 여행, 음식, 생활 후기 및 정보 제공</li>
                <li>회원 대상 공지 및 운영 안내</li>
                </ul>
                <p class="mb-0 mt-2">서비스 내용은 운영상 필요에 따라 변경될 수 있습니다.</p>
            </li>
            <li class="mb-3">
                <strong>제3조 (이용자의 의무)</strong>
                <p class="mb-2">이용자는 다음 행위를 하여서는 안 됩니다.</p>
                <ul class="mb-0">
                <li>법령 또는 공공질서에 위반되는 행위</li>
                <li>사이트 운영을 방해하는 행위</li>
                <li>타인의 권리를 침해하는 행위</li>
                <li>타인의 개인정보를 무단으로 수집·이용·제공하거나 게시하는 행위</li>
                <li>허위 정보 기재 또는 타인 정보 도용</li>
                <li>서비스 내 자료의 무단 복제, 배포, 상업적 이용</li>
                </ul>
            </li>
            <li class="mb-3">
                <strong>제4조 (지적재산권)</strong>
                <p class="mb-0">사이트에 게시된 모든 콘텐츠의 저작권은 사이트 또는 정당한 권리자에게 있으며, 무단 복제 및 배포를 금합니다.</p>
            </li>
            <li class="mb-3">
                <strong>제5조 (책임의 제한)</strong>
                <p class="mb-0">사이트는 제공하는 정보의 정확성에 대해 보장하지 않으며, 이용자가 이를 이용하여 발생한 손해에 대해 책임을 지지 않습니다.</p>
            </li>
            <li class="mb-3">
                <strong>제6조 (약관의 변경)</strong>
                <p class="mb-0">본 약관은 변경될 수 있으며, 변경 시 사이트를 통해 공지합니다.</p>
            </li>
            <li class="mb-0">
                <strong>부칙</strong>
                <p class="mb-0">본 약관은 2026년 2월 10일(예정)부터 적용됩니다.</p>
            </li>
            </ol>
        </section>
    </main>
@endsection
