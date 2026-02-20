<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 사용자 레퍼지토리
 */
class UserRepository
{
    /**
     * 사용자 등록 처리
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * 이메일로 사용자 조회
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * 아이디로 사용자 조회
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::where('idx', $id)->first();
    }

    /**
     * 사용자 푸시 수신 동의값 업데이트
     *
     * @param int $id
     * @param string $agree
     * @return bool
     */
    public function updatePushNotificationAgree(int $id, string $agree): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        return $user->update([
            'push_notification_agree' => $agree,
        ]);
    }

    /**
     * 푸시 수신 동의 사용자 idx 목록 조회
     *
     * @param array<int, int|string> $userIds
     * @return array<int, int>
     */
    public function getPushEnabledUserIds(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        return User::whereIn('idx', $userIds)
            ->where('push_notification_agree', 1)
            ->pluck('idx')
            ->map(function ($id): int {
                return (int) $id;
            })
            ->all();
    }

    /**
     * 회원 목록 조회
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return User::orderByDesc('create_datetime')
            ->when(!empty($filters['search_name']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search_name'] . '%');
            })
            ->when(!empty($filters['search_nickname']), function ($q) use ($filters) {
                $q->where('nick_name', 'like', '%' . $filters['search_nickname'] . '%');
            })
            ->when(!empty($filters['search_gender']), function ($q) use ($filters) {
                $q->where('sex', $filters['search_gender']);
            })
            ->when(isset($filters['search_marketing']) && $filters['search_marketing'] !== '', function ($q) use ($filters) {
                $q->where('marketing_info_agree', (int) $filters['search_marketing']);
            })
            ->when(!empty($filters['search_grade']), function ($q) use ($filters) {
                $q->where('level', $filters['search_grade']);
            })
            ->when(!empty($filters['search_status']), function ($q) use ($filters) {
                if ($filters['search_status'] === 'email_pending') {
                    $q->whereNull('email_verify_datetime');
                }

                if ($filters['search_status'] === 'password_reset') {
                    $q->where('change_password_flag', 1);
                }
            })
            ->paginate($perPage);
    }
}
