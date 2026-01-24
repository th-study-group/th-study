<?php
return [
    'auth' => [
        'users.account.edit' => 'mypage',
        'users.account.withdrawal' => 'mypage',
        'users.account.password_reset' => 'mypage',
        'mypage.inquiries' => 'mypage',
    ],

    'menus' => [
        'mypage' => [
            'users.account.edit' => [
                'title' => '내 정보 변경',
                //'params' => [
                //    'idx' => $accountIdx,
                //    'org_id' => $orgId,
                //],
            ],
            'users.account.password_reset' => [
                'title' => '비밀번호 변경'
            ],
            'users.account.withdrawal' => [
                'title' => '회원 탈퇴 '
            ],
        ],
    ],
];
