<?php

namespace App\Services\Mcp\Tools;

use App\Repositories\Mcp\Tools\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 사용자 MCP API 서비스
 */
class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * 사용자 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function getUsers(array $data) : LengthAwarePaginator
    {
        $users = $this->userRepository->paginateUsers($data);

        Log::info('[User][MCP] Service 조회 완료', [
            'user_idx' => Auth::id(),
            'parameters' => $data,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            'count' => $users->count(),
        ]);

        return $users;
    }


}