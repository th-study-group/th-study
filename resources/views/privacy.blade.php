@extends('layouts.app')

@section('title', '개인정보방침')

@section('content')
    <main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
        <section class="p-4 p-lg-5 rounded-3 shadow-sm" style="background:#f1e8dd; border:1px solid #e2d4c3;">
            <h1 class="h3 fw-bold mb-3">개인정보처리방침</h1>
            <p class="mb-4">{{ config('app.name') }}은(는) 개인정보 보호법 등 관련 법령을 준수하며, 이용자의 개인정보 보호를 중요하게 생각합니다.</p>

            <ol class="mb-4">
            <li class="mb-3">
                <strong>수집하는 개인정보 항목</strong>
                <p class="mb-2">본 사이트는 서비스 제공을 위해 아래와 같은 개인정보를 수집할 수 있습니다.</p>
                <ul>
                <li>필수항목: 이름, 휴대폰번호, 이메일, 생년월일, 성별</li>
                <li>선택항목: 집주소</li>
                </ul>
                <p class="mb-0">※ 단, 단순 방문 시 개인정보를 수집하지 않습니다.</p>
            </li>
            <li class="mb-3">
                <strong>개인정보 수집 방법</strong>
                <ul class="mb-0">
                <li>회원가입, 문의하기, 이메일 인증 등을 통해 수집</li>
                </ul>
            </li>
            <li class="mb-3">
                <strong>개인정보의 이용 목적</strong>
                <ul class="mb-0">
                <li>서비스 제공 및 문의 응대</li>
                <li>공지사항 전달</li>
                <li>서비스 개선 및 운영 관리</li>
                <li>마케팅/프로모션 안내 및 푸시/알림 발송</li>
                </ul>
            </li>
            <li class="mb-3">
                <strong>개인정보 보유 및 이용 기간</strong>
                <p class="mb-0">회원 탈퇴 또는 개인정보 삭제 요청 전까지 보관하며, 서비스 운영을 위해 무기한 보관할 수 있습니다. 단, 관련 법령에 따라 보관이 필요한 경우 해당 기간 동안 보관합니다.</p>
            </li>
            <li class="mb-3">
                <strong>개인정보의 제3자 제공</strong>
                <p class="mb-0">본 사이트는 이용자의 개인정보를 외부에 제공하지 않습니다.</p>
            </li>
            <li class="mb-3">
                <strong>개인정보 보호를 위한 조치</strong>
                <p class="mb-0">개인정보는 접근 제한 및 관리적·기술적 보호조치를 통해 안전하게 관리됩니다.</p>
            </li>
            <li class="mb-3">
                <strong>개인정보 보호 책임자</strong>
                <ul class="mb-0">
                <li>관리자: 이태희</li>
                <li>이메일: {{ config('mail.from.address') }}</li>
                </ul>
            </li>
            <li class="mb-0">
                <strong>개인정보처리방침 변경</strong>
                <p class="mb-0">본 개인정보처리방침은 변경될 수 있으며, 변경 시 본 페이지를 통해 공지합니다.</p>
            </li>
            </ol>

            <p class="mb-0 text-muted">시행일자: 2026년 2월 10일</p>
        </section>
    </main>
@endsection
