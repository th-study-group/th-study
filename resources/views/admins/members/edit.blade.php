@extends('layouts.app')

@section('title', '상세내역')

@section('content')
    <section class="col-12 col-lg-8 mx-auto">
        <form id="guest_post_form" method="POST" action="{{ route('admins.members.update', ['idx' => $idx]) }}">
            @csrf
            @method('PATCH')
            <div class="board-card bg-white rounded-3 p-3 p-lg-4 shadow-sm">
                <div class="board-head d-flex flex-column gap-2 gap-lg-3">
                    <div>
                        <h2 class="board-title h5 mb-1">회원정보</h2>
                        <p class="text-secondary small mb-0 board-ellipsis">회원 상세정보를 확인할수있습니다.</p>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">이름</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">심수민</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">닉네임</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">수민짱</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">이메일</span>
                        <div class="board-field board-ellipsis bg-light rounded-3 px-3 py-2">sumin@example.com</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">생년월일</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">1992-03-21</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">성별</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">여</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">연락처</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">010-1234-5678</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">주소</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">서울특별시 강남구 테헤란로 123</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">개인정보동의여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">동의</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">마케팅동의여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">미동의</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원등급</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">일반</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">최근접속일자</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">2026-01-31 19:22:10</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원가입일시</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">2025-10-12 09:01:02</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">회원인증일시</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">2025-10-12 09:05:22</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">비밀번호 변경진행여부</span>
                        <div class="board-field bg-light rounded-3 px-3 py-2">진행중</div>
                    </div>
                    <div class="mb-3">
                        <span class="form-label small text-secondary d-block mb-1">비고</span>
                        <textarea id="memo"
                                  name="memo"
                                  class="form-control board-textarea bg-light rounded-3 px-3 py-2 border-0"
                                  rows="6"
                                  placeholder="비고를 입력해 주세요"></textarea>
                    </div>
                </div>

            </div>

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 board-status-actions">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" id="btn_post_save" class="btn btn-primary">적용</button>
                <a href="{{ route('admins.members.index') }}" class="btn btn-secondary">목록</a>
            </div>
        </div>
        </form>
    </section>
@endsection

@section('script')
    <script>
        $(function(){

            $("#btn_post_save").on("click", function() {
                if (!confirm('적용하시겠습니까?')) {
                    return;
                }
            });
        });
    </script>
@endsection
