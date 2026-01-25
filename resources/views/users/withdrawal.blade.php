@extends('layouts.app')

@section('title', '회원탈퇴')

@section('content')
    <main class="col-lg-10 content-col">
        <section class="bg-white p-4 p-lg-5 rounded-3 shadow-sm" style="font-family: 'Noto Sans KR', 'Apple SD Gothic Neo', var(--bs-font-sans-serif);">
            <div class="text-center fs-3 fw-bold text-secondary mb-4" style="letter-spacing: 0;">Withdraw</div>

            @if ($errors->any())
                <div class="alert alert-warning d-flex align-items-center gap-2 small mb-4" role="alert">
                    <span class="badge text-bg-warning text-dark">경고</span>
                    <span>회원탈퇴 전 안내사항을 확인해주세요.</span>
                </div>
            @endif

            <form id="form_account_withdrawal" name="form_account_withdrawal" method="POST" action="{{ route('users.account.soft.delete') }}">
                @csrf
                @method('PATCH')
                <div class="w-100">
                    <div class="border rounded-3 bg-light p-3 mb-4">
                        <div class="fw-semibold text-secondary mb-2">회원탈퇴 안내</div>
                        <ul class="small text-secondary mb-0">
                            <li>회원탈퇴 시 모든 개인정보는 즉시 삭제되며 복구할 수 없습니다.</li>
                            <li>탈퇴 후 동일 이메일로 재가입이 제한될 수 있습니다.</li>
                            <li>탈퇴 시 보유한 혜택 및 서비스 이용 기록이 소멸됩니다.</li>
                            <li>탈퇴가 정상적으로 완료되면 이메일 안내가 발송됩니다.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="withdrawal_confirm"
                                name="withdrawal_confirm"
                                class="form-check-input @error('withdrawal_confirm') is-invalid @enderror"
                                value="Y"
                                @checked(old('withdrawal_confirm'))
                            >
                            <label class="form-check-label" for="withdrawal_confirm">
                                위 내용을 모두 확인하였으며, 회원탈퇴에 동의합니다. (필수)
                            </label>
                        </div>
                        @error('withdrawal_confirm')
                            <div class="invalid-feedback d-block small text-break">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger w-100">회원탈퇴</button>
                </div>
            </form>
        </section>
    </main>
@endsection
