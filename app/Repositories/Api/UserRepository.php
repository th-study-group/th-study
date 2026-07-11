<?php

namespace App\Repositories\Api;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 사용자 API 레포지토리
 */
class UserRepository
{
    /**
     * 사용자 목록 반환
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginateUsers(array $data) : LengthAwarePaginator
    {
        $perPage = $data['per_page'] ?? 20;

        $users = User::query()
            ->select(
                'idx',
                'email',
                'name',
                'nick_name',
                'birth_date',
                'sex',
                'address',
                'phone',
                'personal_info_agree',
                'marketing_info_agree',
                'push_notification_agree',
                'level',
                'ip',
                'last_access_datetime',
                'memo',
                'create_datetime',
                'create_user_idx',
            )
            ->whereNotNull('email_verify_datetime')
            ->when(!empty($data['idx']), function ($query) use ($data) {
                $query->where('idx', $data['idx']);
            })
            ->when(!empty($data['name']), function ($query) use ($data) {
                $query->where('name', 'like', "%{$data['name']}%");
            })
            ->when(!empty($data['nick_name']), function ($query) use ($data) {
                $query->where('nick_name', 'like', "%{$data['nick_name']}%");
            })
            ->when(!empty($data['birth_year']), function ($query) use ($data) {
                $query->whereYear('birth_date', $data['birth_year']);
            })
            ->when(!empty($data['sex']), function ($query) use ($data) {
                $query->where('sex', $data['sex']);
            })
            ->when(!empty($data['marketing_info_agree']), function ($query) use ($data) {
                $query->where('marketing_info_agree', $data['marketing_info_agree']);
            })
            ->when(!empty($data['level']), function ($query) use ($data) {
                $query->where('level', $data['level']);
            })
            ->orderBy('create_datetime', 'desc')
            ->paginate($perPage);

        return $users;
    }

    /**
     * 이메일로 인증된 사용자 조회
     */
    public function findVerifiedByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->whereNotNull('email_verify_datetime')
            ->first();
    }
}