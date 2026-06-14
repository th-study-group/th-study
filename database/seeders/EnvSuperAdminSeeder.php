<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ENV 설정값 통해서 관리자 계정 만들기 (개발/로컬환경)
 */
class EnvSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'test99@test.com');
        $plainPassword = env('SUPERADMIN_PASSWORD', '12345678!*V');

        // .env 값 없으면 에러로 멈추게 (안전)
        if (!$email || !$plainPassword) {
            throw new Exception('SUPERADMIN_EMAIL / SUPERADMIN_PASSWORD 를 .env에 설정하세요.');
        }

	    // 비밀번호 규칙(너가 준 규칙) 체크
		$regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/';
		$len = Str::length($plainPassword);

		if ($len < 8 || $len > 15 || !preg_match($regex, $plainPassword)) {
			throw new Exception('SUPERADMIN_PASSWORD 규칙 위반: 8~15자, 대문자/소문자/숫자 포함');
        }

		// 이미 있으면 업데이트, 없으면 생성 (업데이트 시각 변경 방지)
		User::withoutTimestamps(function () use ($email, $plainPassword): void {
			$user = User::firstOrNew(['email' => $email]);

			$user->fill([
				'name' => 'OpenAI',
				'password' => $plainPassword,
				'nick_name' => 'OpenAI심사',
				'birth_date' => '1990-01-01',
				'sex' => 'M',
				'phone' => '01000000000',
				'address' => 'Review Account',
				'personal_info_agree' => 'Y',
				'marketing_info_agree' => 'N',
				'level' => 'normal',
				'ip' => '0.0.0.0',
				'email_verify_datetime' => now(),
				'change_password_flag' => 0,
				'create_datetime' => now(),
			]);

			$user->saveQuietly();
		});
    }
}
