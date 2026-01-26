@extends('layouts.app')

@section('title', '회원정보 수정')

@section('content')
    <main class="col-lg-10 content-col">
        <div class="row justify-content-center">
            <div class="col-12 col-md-11 col-lg-9 col-xl-7">
                <section class="border border-2 border-secondary-subtle rounded-4 bg-white p-4 p-md-5 shadow-sm" style="font-family: 'Noto Sans KR', 'Apple SD Gothic Neo', var(--bs-font-sans-serif);">
                    <div class="text-center fs-3 fw-bold text-secondary mb-4" style="letter-spacing: 0;">Profile</div>

            @if ($errors->any())
                <div class="alert alert-warning d-flex align-items-center gap-2 small mb-4" role="alert">
                    <span class="badge text-bg-warning text-dark">경고</span>
                    <span>회원정보 수정 실패사유를 확인하세요.</span>
                </div>
            @endif

            <form id="form_account_edit" name="form_account_edit" method="POST" action="{{ route('users.account.update') }}">
                @csrf
                @method('PUT')
                <div class="w-100">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">이메일</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="example@email.com"
                            value="{{ old('email', optional(auth()->user())->email) }}"
                            disabled
                        >
                        <div class="form-text small text-muted">이메일은 변경할 수 없습니다.</div>
                        @error('email')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">이름</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="홍길동"
                            value="{{ old('name', optional(auth()->user())->name) }}"
                        >
                        @error('name')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">닉네임</label>
                        <input
                            type="text"
                            id="nick_name"
                            name="nick_name"
                            class="form-control @error('nick_name') is-invalid @enderror"
                            placeholder="닉네임을 입력해주세요"
                            value="{{ old('nick_name', optional(auth()->user())->nick_name) }}"
                        >
                        @error('nick_name')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">생년월일</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-secondary">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <input
                                type="text"
                                id="birth_date"
                                name="birth_date"
                                class="form-control @error('birth_date') is-invalid @enderror"
                                placeholder="1990-01-01"
                                value="{{ old('birth_date', optional(auth()->user())->birth_date) }}"
                            >
                        </div>
                        @error('birth_date')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small d-block">성별</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input
                                    type="radio"
                                    id="sex_man"
                                    name="sex"
                                    class="form-check-input @error('sex') is-invalid @enderror"
                                    value="M"
                                    @checked(old('sex', optional(auth()->user())->sex) === 'M')
                                >
                                <label class="form-check-label" for="sex_man">남성</label>
                            </div>
                            <div class="form-check">
                                <input
                                    type="radio"
                                    id="sex_woman"
                                    name="sex"
                                    class="form-check-input @error('sex') is-invalid @enderror"
                                    value="W"
                                    @checked(old('sex', optional(auth()->user())->sex) === 'W')
                                >
                                <label class="form-check-label" for="sex_woman">여성</label>
                            </div>
                        </div>
                        @error('sex')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">핸드폰</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="01012345678"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            value="{{ old('phone', optional(auth()->user())->phone) }}"
                        >
                        @error('phone')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold small">주소 <span class="text-danger">(선택)</span></label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="서울특별시 강남구 테헤란로 123"
                            value="{{ old('address', optional(auth()->user())->address) }}"
                        >
                        @error('address')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="personal_info_agree"
                                name="personal_info_agree"
                                class="form-check-input @error('personal_info_agree') is-invalid @enderror"
                                value="Y"
                                @checked(old('personal_info_agree', optional(auth()->user())->personal_info_agree) === 'Y')
                            >
                            <label class="form-check-label" for="personal_info_agree">개인정보동의(필수)</label>
                        </div>
                        @error('personal_info_agree')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="marketing_info_agree"
                                name="marketing_info_agree"
                                class="form-check-input"
                                value="Y"
                                @checked(old('marketing_info_agree', optional(auth()->user())->marketing_info_agree) === 'Y')
                            >
                            <label class="form-check-label" for="marketing_info_agree">마케팅동의</label>
                        </div>
                        @error('marketing_info_agree')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end align-items-center mb-2">
                        <a href="{{ route('users.account.withdrawal') }}" class="link-danger small text-decoration-none">회원탈퇴</a>
                    </div>
                    <button type="submit" class="btn btn-dark border-0 w-100">회원정보 수정</button>
                </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
@endsection
