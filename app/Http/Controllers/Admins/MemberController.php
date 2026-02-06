<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\MemberSearchRequest;
use App\Http\Requests\Admins\MemberUpdateRequest;
use App\Services\UserService;
use Illuminate\View\View;

/**
 * 회원관리 
 */
class MemberController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * 회원목록
     *
     * @return void
     */
    public function index(MemberSearchRequest $request) : View
    {
        $filters = $request->validated();
        $members = $this->userService->getMembers($filters);
        $members->appends($filters);

        return view('admins.members.index', [
            'members' => $members,
            'filters' => $filters,
            'terms' => config('const.terms'),
            'sexList' => config('const.sex'),
            'gradeList' => config('member.levels'),
        ]);
    }

    /**
     * 회원 상세정보 및 수정
     *
     * @return void
     */
    public function edit(string $idx) : View
    {
        $member = $this->userService->getMemberById($idx);

        return view('admins.members.edit', [
            'idx' => $idx,
            'member' => $member,
            'terms' => config('const.terms'),
            'sexList' => config('const.sex'),
            'gradeList' => config('member.levels'),
        ]);
    }

    /**
     * 관리자 수정 처리
     *
     * @param Request $request
     * @return void
     */
    public function update(MemberUpdateRequest $request, string $idx)
    {
        $member = $this->userService->getMemberById($idx);
        $validated = $request->validated();

        $this->userService->updateMemo($member, $validated);

        return to_route('admins.members.edit', ['idx' => $idx]);
    }
    
}
