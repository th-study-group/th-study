@extends('layouts.app')

@section('title', '비밀번호 변경')

@section('content')
    <main class="col-lg-10 content-col">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-9 col-xl-7">
                <section class="border border-2 border-secondary-subtle rounded-4 bg-white p-4 p-md-5 shadow-sm" style="font-family: 'Noto Sans KR', 'Apple SD Gothic Neo', var(--bs-font-sans-serif);">
                    <div class="text-center fs-3 fw-bold text-secondary mb-4" style="letter-spacing: 0;">Password</div>

            @if ($errors->any())
                <div class="alert alert-warning d-flex align-items-center gap-2 small mb-4" role="alert">
                    <span class="badge text-bg-warning text-dark">경고</span>
                    <span>비밀번호 변경 안내를 확인해주세요.</span>
                </div>
            @endif

            <form id="form_password_reset" name="form_password_reset" method="POST" action="#">
                @csrf
                <div class="w-100">
                    <div class="border rounded-3 bg-light p-3 mb-4">
                        <div class="fw-semibold text-secondary mb-2">비밀번호 변경 안내</div>
                        <ul class="small text-secondary mb-0">
                            <li>비밀번호 변경하기 버튼을 클릭하면 자동으로 로그아웃됩니다.</li>
                            <li>다시 로그인하면 비밀번호 변경 화면으로 이동합니다.</li>
                            <li>정상적으로 변경되면 안내 메일이 발송됩니다.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="password_reset_confirm"
                                name="password_reset_confirm"
                                class="form-check-input @error('password_reset_confirm') is-invalid @enderror"
                                value="Y"
                                @checked(old('password_reset_confirm'))
                            >
                            <label class="form-check-label" for="password_reset_confirm">
                                위 내용을 확인하였으며, 이메일 인증을 진행합니다. (필수)
                            </label>
                        </div>
                        @error('password_reset_confirm')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-dark border-0 w-100">비밀번호 변경 하기</button>
                </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
@endsection
