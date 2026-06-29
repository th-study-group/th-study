@extends('layouts.app')

@section('title', '회원가입')

@section('content')
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                <div class="border border-2 border-secondary-subtle rounded-4 bg-white p-4 p-md-5 shadow-sm" style="font-family: 'Noto Sans KR', 'Apple SD Gothic Neo', var(--bs-font-sans-serif);">
                    <div class="text-center fs-3 fw-bold text-secondary mb-4" style="letter-spacing: 0;">Welcome, new member!</div>

                    @if ($errors->any())
                        <div class="alert alert-warning d-flex align-items-center gap-2 small mb-4" role="alert">
                            <span class="badge text-bg-warning text-dark">경고</span>
                            <span>회원가입 실패사유를 확인하세요.</span>
                        </div>
                    @endif

                    <form id="form_register" name="form_register" method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="w-100">
                            <div class="mb-3">
                                <label class="form-label text-secondary fw-semibold small">이메일</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="example@email.com"
                                    value="{{ old('email') }}"
                                >
                                @error('email')
                                    <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary fw-semibold small">비밀번호</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="비밀번호를 입력해주세요."
                                    value=""
                                >
                                @error('password')
                                    <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary fw-semibold small">비밀번호 확인</label>
                                <input
                                    type="password"
                                    id="password_confirm"
                                    name="password_confirm"
                                    class="form-control @error('password_confirm') is-invalid @enderror"
                                    placeholder="비밀번호를 한번 더 입력해주세요."
                                    value=""
                                >
                                <div id="password_confirm_msg" class="form-text small text-danger"></div>
                                @error('password_confirm')
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
                                    value="{{ old('name') }}"
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
                                    value="{{ old('nick_name') }}"
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
                                        value="{{ old('birth_date') }}"
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
                                            @checked(old('sex') === 'M')
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
                                            @checked(old('sex') === 'W')
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
                                    value="{{ old('phone') }}"
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
                                    value="{{ old('address') }}"
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
                                        @checked(old('personal_info_agree'))
                                        value="Y"
                                    >
                                    <label class="form-check-label" for="personal_info_agree">개인정보동의 (필수)</label>
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
                                        @checked(old('marketing_info_agree'))
                                    >
                                    <label class="form-check-label" for="marketing_info_agree">마케팅동의 (선택)</label>
                                </div>
                                @error('marketing_info_agree')
                                    <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end align-items-center mb-2">
                                <a href="{{ route('login') }}" class="link-primary small text-decoration-none">로그인</a>
                            </div>
                            <button type="button" id="btn_join_completed" class="btn btn-dark border-0 w-100">회원가입 완료</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            initBirthDatePicker('#birth_date', {
                mobileSelectHeader: true
            });

            $("#btn_join_completed").on("click", function() {
                if ($.trim($('#email').val()) === '') {
                    alert('이메일을 입력해주세요.');
                    $('#email').focus();
                    return;
                }

                if ($.trim($('#password').val()) === '') {
                    alert('비밀번호를 입력해주세요.');
                    $('#password').focus();
                    return;
                }

                if ($.trim($('#password_confirm').val()) === '') {
                    alert('비밀번호 확인을 입력해주세요.');
                    $('#password_confirm').focus();
                    return;
                }

                if ($('#password').val() !== $('#password_confirm').val()) {
                    alert('비밀번호와 비밀번호 확인 값이 일치하지 않습니다.');
                    $('#password_confirm').focus();
                    return;
                }

                if ($.trim($('#name').val()) === '') {
                    alert('이름을 입력해주세요.');
                    $('#name').focus();
                    return;
                }

                if ($.trim($('#nick_name').val()) === '') {
                    alert('닉네임을 입력해주세요.');
                    $('#nick_name').focus();
                    return;
                }

                if ($.trim($('#birth_date').val()) === '') {
                    alert('생년월일을 입력해주세요.');
                    $('#birth_date').focus();
                    return;
                }

                if ($.trim($('#phone').val()) === '') {
                    alert('핸드폰을 입력해주세요.');
                    $('#phone').focus();
                    return;
                }

                if ($('input[name="sex"]').is(':checked') === false) {
                    alert('성별 값을 입력해주세요.');
                    $('#sex_man').focus();
                    return;
                }

                if (!$('#personal_info_agree').is(':checked')) {
                    alert('개인정보동의 체크해주세요.');
                    $('#personal_info_agree').focus();
                    return;
                }

                $("#form_register").submit();
            });

            $('#password_confirm').on('input change', updatePasswordConfirmMsg);
            $('#password').on('input change', updatePasswordConfirmMsg);
        });

        function updatePasswordConfirmMsg() {
            const password = $('#password').val();
            const confirm = $('#password_confirm').val();
            const $msg = $('#password_confirm_msg');

            if (!confirm) {
                $msg.text('');
                return;
            }

            if (password !== confirm) {
                $msg.text('비밀번호와 비밀번호 확인 값이 일치하지 않습니다.');
            } else {
                $msg.text('');
            }
        }
    </script>
@endsection
