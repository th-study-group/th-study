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
     * 회원 목록 조회
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = User::orderByDesc('create_datetime');

        if (!empty($filters['search_name'])) {
            $query->where('name', 'like', '%' . $filters['search_name'] . '%');
        }

        if (!empty($filters['search_nickname'])) {
            $query->where('nick_name', 'like', '%' . $filters['search_nickname'] . '%');
        }

        if (!empty($filters['search_gender'])) {
            $query->where('sex', $filters['search_gender']);
        }

        if (isset($filters['search_marketing']) && $filters['search_marketing'] !== '') {
            $query->where('marketing_info_agree', (int) $filters['search_marketing']);
        }

        if (!empty($filters['search_grade'])) {
            $query->where('level', $filters['search_grade']);
        }

        if (!empty($filters['search_status'])) {
            if ($filters['search_status'] === 'email_pending') {
                $query->whereNull('email_verify_datetime');
            }

            if ($filters['search_status'] === 'password_reset') {
                $query->where('change_password_flag', 1);
            }
        }

        return $query->paginate($perPage);
    }
}
