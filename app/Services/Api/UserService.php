<?php

namespace App\Services\Api;

use App\Events\UserLoginAttemptedEvent;
use App\Models\User;
use App\Repositories\Api\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * 사용자 API 서비스
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

    /**
     * 로그인 시도 기록
     *
     * @param string $email
     * @param string $ip
     * @param string $userAgent
     * @param bool $success
     * @param string $provider
     * @param string|null $reason
     * @return User|null
     */
    public function recordLoginAttempt(
        string $email,
        string $ip,
        string $userAgent,
        bool $success,
        string $provider = 'local',
        ?string $reason = null
    ): ?User {
        $user = $this->userRepository->findVerifiedByEmail($email);

        event(new UserLoginAttemptedEvent(
            email: $email,
            accessUserIdx: $user?->idx,
            ip: $ip,
            userAgent: $userAgent,
            success: $success,
            provider: $provider,
            reason: $reason
        ));

        return $user;
    }

}