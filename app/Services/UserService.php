<?php

namespace App\Services;

use App\Events\UserLoginAttemptedEvent;
use App\Mail\UserNoticeMail;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Jobs\SendMailJob;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * 아이디로 사용자 조회 (공통)
     *
     * @param int $id
     * @return User
     */
    public function findById(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new ModelNotFoundException();
        }

        return $user;
    }

    /**
     * 관리자 회원 목록 조회
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getMembers(array $filters): LengthAwarePaginator
    {
        $page = $filters['page'] ?? 1;

        $members = $this->userRepository->paginate($filters, 20);

        Log::info('[Admin][Member][List] 조회 완료', [
            'user_idx' => auth()->id(),
            'page' => $page,
            'ip' => request()->ip(),
        ]);

        return $members;
    }

    /**
     * 관리자 회원 상세 조회
     *
     * @param int $id
     * @return User
     */
    public function getMemberById(int $id): User
    {
        return $this->findById($id);
    }

    /**
     * 관리자 메모 수정
     *
     * @param User $user
     * @param array $payload
     * @return User
     */
    public function updateMemo(User $user, array $payload): User
    {
        $userIdx = auth()->id();

        $user->forceFill([
            'memo' => $payload['memo'] ?? null,
            'update_user_idx' => $userIdx,
        ])->saveQuietly();

        Log::info('[Admin][Member][Update] 메모 수정 완료', [
            'user_idx' => $userIdx,
            'target_user_idx' => $user->idx,
            'ip' => $payload['ip'] ?? request()->ip(),
        ]);

        return $user;
    }

    /**
     * 사용자 수정 처리
     *
     * @param array $payload
     * @return bool
     */
    public function update(array $payload = []): bool
    {
        try {
            if (empty($payload['user_idx']) || empty($payload['ip'])) {
                Log::warning('User update failed', [
                    'action' => 'update',
                    'model' => 'User',
                    'ip' => $payload['ip'] ?? request()->ip(),
                    'reason' => 'missing_payload',
                ]);

                return false;
            }

            $id = (int) $payload['user_idx'];
            $ip = $payload['ip'];
            $user = $this->userRepository->findById($id);

            if (!$user) {
                Log::warning('User update failed', [
                    'action' => 'update',
                    'model' => 'User',
                    'user_idx' => $id,
                    'ip' => $ip,
                    'reason' => 'not_found',
                ]);

                return false;
            }

            return DB::transaction(function () use ($user, $payload, $ip, $id) {
                $user->update([
                    'name' => $payload['name'],
                    'nick_name' => $payload['nick_name'],
                    'birth_date' => $payload['birth_date'],
                    'sex' => $payload['sex'],
                    'phone' => $payload['phone'],
                    'address' => $payload['address'] ?? null,
                    'personal_info_agree' => $payload['personal_info_agree'],
                    'marketing_info_agree' => $payload['marketing_info_agree'] ?? null,
                    'update_user_idx' => $id,
                ]);

                Log::info('User update succeeded', [
                    'action' => 'update',
                    'model' => 'User',
                    'user_idx' => $user->idx,
                    'email' => $user->email,
                    'ip' => $ip,
                    'payload' => $payload,
                ]);

                return true;
            });
        } catch (Throwable $e) {
            Log::error('User update failed', [
                'action' => 'update',
                'model' => 'User',
                'user_idx' => $id,
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * 회원탈퇴 처리
     *
     * @param array $payload
     * @return bool
     */
    public function withdraw(array $payload = []): bool
    {
        try {
            if (empty($payload['user_idx']) || empty($payload['ip'])) {
                Log::warning('User withdrawal failed', [
                    'action' => 'withdraw',
                    'model' => 'User',
                    'ip' => $payload['ip'] ?? request()->ip(),
                    'reason' => 'missing_payload',
                ]);

                return false;
            }

            $user = $this->userRepository->findById((int) $payload['user_idx']);

            if (!$user) {
                Log::warning('User withdrawal failed', [
                    'action' => 'withdraw',
                    'model' => 'User',
                    'user_idx' => $payload['user_idx'],
                    'ip' => $payload['ip'],
                    'reason' => 'not_found',
                ]);

                return false;
            }

            DB::transaction(function () use ($user, $payload) {
                $user->update([
                    'delete_user_idx' => $user->idx,
                ]);
                $user->delete();

                Log::info('User withdrawal succeeded', [
                    'action' => 'withdraw',
                    'model' => 'User',
                    'user_idx' => $user->idx,
                    'email' => $user->email,
                    'ip' => $payload['ip'],
                ]);
            });

            $subject = sprintf('[%s] %s님 회원탈퇴가 정상적으로 완료되었습니다.', config('app.name'), $user->name);

            $params =  [
                'name' => $user->name,
                'siteUrl' => config('app.url'),
            ];

            SendMailJob::dispatch(
                $user->email,
                new UserNoticeMail(
                    mailSubject: $subject,
                    bladeName: 'withdrawal_notice',
                    params: $params,
                ),
                '회원탈퇴알림',
                null,
                $user->idx
            );

            return true;
        } catch (Throwable $e) {
            Log::error('User withdrawal failed', [
                'action' => 'withdraw',
                'model' => 'User',
                'user_idx' => $payload['user_idx'] ?? null,
                'ip' => $payload['ip'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * 비밀번호 변경 요청 처리
     *
     * @param array $payload
     * @return bool
     */
    public function requestPasswordChange(array $payload = []): bool
    {
        try {
            if (empty($payload['user_idx']) || empty($payload['ip'])) {
                Log::warning('Password change request failed', [
                    'action' => 'password_change_request',
                    'model' => 'User',
                    'ip' => $payload['ip'] ?? request()->ip(),
                    'reason' => 'missing_payload',
                ]);

                return false;
            }

            $user = $this->userRepository->findById((int) $payload['user_idx']);

            if (!$user) {
                Log::warning('Password change request failed', [
                    'action' => 'password_change_request',
                    'model' => 'User',
                    'user_idx' => $payload['user_idx'],
                    'ip' => $payload['ip'],
                    'reason' => 'not_found',
                ]);

                return false;
            }

            DB::transaction(function () use ($user, $payload) {
                $user->forceFill([
                    'change_password_flag' => 1,
                ])->saveQuietly();

                Log::info('Password change request succeeded', [
                    'action' => 'password_change_request',
                    'model' => 'User',
                    'user_idx' => $user->idx,
                    'email' => $user->email,
                    'ip' => $payload['ip'],
                ]);

                $subject = sprintf('[%s] %s님 비밀번호 변경 요청', config('app.name'), $user->name);

                $params = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'siteUrl' => route('login'),
                ];

                SendMailJob::dispatchSync(
                    $user->email,
                    new UserNoticeMail(
                        mailSubject: $subject,
                        bladeName: 'password_change_request',
                        params: $params,
                    ),
                    '비밀번호변경',
                    null,
                    $user->idx
                );
            });

            return true;
        } catch (Throwable $e) {
            Log::error('Password change request failed', [
                'action' => 'password_change_request',
                'model' => 'User',
                'user_idx' => $payload['user_idx'] ?? null,
                'ip' => $payload['ip'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
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

                $subject = sprintf('[%s] %s님 비밀번호 변경이 완료되었습니다.', config('app.name'), $user->name);

                $params = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'siteUrl' => route('login'),
                ];

                SendMailJob::dispatch(
                    $user->email,
                    new UserNoticeMail(
                        mailSubject: $subject,
                        bladeName: 'password_change_complete',
                        params: $params,
                    ),
                    '비밀번호변경완료',
                    null,
                    $user->idx
                );

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
