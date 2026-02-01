<footer class="bg-dark text-secondary py-4">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex flex-wrap gap-3 border-bottom border-secondary pb-3 mb-3">
            <a class="text-white text-decoration-none" href="{{ route('privacy') }}">개인정보처리방침</a>
            <a class="text-white text-decoration-none" href="{{ route('terms') }}">이용약관</a>
        </div>
        <div class="row g-3 align-items-start">
            <div class="col-12 col-lg-7">
                <h6 class="text-white mb-2">{{ config('app.name') }}</h6>
                <div class="text-white-50 small">
                <div>관리자 : 이태희</div>
                <div>이메일 : developerkimtakgu@gmail.com</div>
                <div>주소 : 경기도 안산시 단원구 시화호수로 633 (반달섬)</div>
                </div>
            </div>
            <div class="col-12 col-lg-5 text-center text-lg-end mt-2 mt-lg-0">
                <div class="text-white-50 small">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
            </div>
        </div>
    </div>
</footer>