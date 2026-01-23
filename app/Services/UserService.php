<?php

namespace App\Services;

use App\Events\UserLoginAttemptedEvent;
use App\Models\User;
use App\Repositories\UserRepository;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * 사용자 등록 처리
     *
     * @param array $payload
     * @param string $ip
     * @return User
     */
    public function register(array $payload, string $ip): User
    {
        return DB::transaction(function () use ($payload, $ip) {

            $user = $this->userRepository->create($payload);

            // 대량 할당 방지 (관리자 처리)
            // 업데이트 처리되어서 update_datetime 변경되는거 방지 
            // 정책에 따라 아래 구분을 서비스에서 해도 되고 레퍼지토리에서 묶어도됨 
            // 업데이트 시 시각은 안함 (로그인이 아니기 때문에)
            $user->forceFill([
                'level' => 'normal',
                 'ip' => $ip
            ])->saveQuietly();

            Log::info('User register created', [
                'action' => 'create',
                'model' => 'User',
                'user_idx' => $user->idx,
                'email' => $user->email,
                'ip' => $ip,
            ]);

            return $user;
        });
    }

    /**
     * 로그인 처리
     *
     * @param array $payload
     * @param string $ip
     * @return bool
     */
    public function authenticate(array $payload, string $ip): bool
    {
        $user = $this->userRepository->findByEmail($payload['email']);
        $userAgent = request()->userAgent() ?? '';

        if (!$user || !Hash::check($payload['password'], $user->password)) {
            event(new UserLoginAttemptedEvent(
                email: $payload['email'],
                accessUserIdx: $user?->idx,
                ip: $ip,
                userAgent: $userAgent,
                success: false,
                provider: 'local',
                reason: 'invalid_credentials'
            ));
            return false;
        }

        $remember = $payload['remember'] ?? false;
        Auth::login($user, $remember);

        $user->forceFill([
            'last_access_datetime' => now(),
            'ip' => $ip,
        ])->saveQuietly();

        // 개인정보 시 강제 로그아웃 위한 변수
        session(['session_version' => $user->session_version]);

        Log::info('User login succeeded', [
            'action' => 'login',
            'model' => 'User',
            'user_idx' => $user->idx,
            'email' => $user->email,
            'ip' => $ip,
        ]);

        event(new UserLoginAttemptedEvent(
            email: $user->email,
            accessUserIdx: $user->idx,
            ip: $ip,
            userAgent: $userAgent,
            success: true,
            provider: 'local'
        ));

        return true;
    }

    /**
     * 비밀번호 변경
     *
     * @param User $user
     * @param string $ip
     * @return bool
     */
    public function changePassword(User $user, string $password, string $ip, array $payload = []): bool
    {
        try {
            $data = array_merge([
                'password' => $password,
            ], $payload);

            return DB::transaction(function () use ($user, $data, $ip) {
                $data['password'] = $data['password'];

                $user->forceFill($data)->save();

                Log::info('Password change flag updated', [
                    'action' => 'update',
                    'model' => 'User',
                    'user_idx' => $user->idx,
                    'email' => $user->email,
                    'ip' => $ip,
                ]);

                return true;
            });
        } catch (Throwable $e) {
            Log::error('Password change flag update failed', [
                'action' => 'update',
                'model' => 'User',
                'user_idx' => $user->idx,
                'email' => $user->email,
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
