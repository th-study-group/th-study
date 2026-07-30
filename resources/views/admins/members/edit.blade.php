@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <form id="guest_post_form" method="POST" action="{{ route('admins.members.update', ['idx' => $idx]) }}">
            @csrf
            @method('PUT')
            <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
                <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                    <div>
                        <h2 class="board-title h5 mb-1">회원정보</h2>
                        <p class="text-secondary small mb-0 board-ellipsis">회원 상세정보를 확인할수있습니다.</p>
                    </div>
                </div>

                <div class="mt-3">
                    @if ($errors->any())
                        <div class="alert alert-warning">회원 정보 수정 실패 사유를 확인해주세요.</div>
                    @endif
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">이름</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $member->name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">닉네임</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $member->nick_name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">이메일</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">{{ $member->email ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">생년월일</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->birth_date?->format('Y-m-d') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">성별</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $sexList[$member->sex ?? ''] ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">연락처</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->phone_formatted }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">주소</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->address ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">개인정보동의여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $terms[(int) $member->getRawOriginal('personal_info_agree')] ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">마케팅동의여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $terms[(int) $member->getRawOriginal('marketing_info_agree')] ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">푸시알림수신동의</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $terms[(int) $member->getRawOriginal('push_notification_agree')] ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원등급</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $gradeList[$member->level ?? ''] ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">인증여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ !empty($member->email_verify_datetime) ? '인증' : '미인증' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">최근접속일자</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->last_access_datetime ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원가입일시</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->create_datetime ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원인증일시</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ $member->email_verify_datetime ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">비밀번호 변경진행여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">{{ (int) ($member->change_password_flag ?? 0) === 1 ? '진행중' : '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">비고</span>
                        <textarea id="memo"
                                  name="memo"
                                  class="form-control board-textarea bg-light rounded-3 px-3 py-2 border-0 @error('memo') is-invalid @enderror"
                                  rows="6"
                                  placeholder="비고를 입력해 주세요">{{ old('memo', $member->memo ?? '') }}</textarea>
                        @error('memo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

            </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" id="btn_post_save" class="btn btn-primary">적용</button>
                <button type="button" id="btn_post_list" class="btn btn-secondary">목록</button>
            </div>
        </div>
        </form>
    </section>
@endsection

@section('script')
    <script>
        $(function(){

            const listUrl = "{{ route('admins.members.index') }}";

            $("#btn_post_list").on("click", function() {
                location.href = listUrl;
            });

            $("#btn_post_save").on("click", function() {
                if (!confirm('적용하시겠습니까?')) {
                    return;
                }
                $('#guest_post_form').submit();
            });
        });
    </script>
@endsection
