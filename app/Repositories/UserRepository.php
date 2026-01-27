<?php

namespace App\Repositories;

use App\Models\User;

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
}
