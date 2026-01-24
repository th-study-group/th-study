<?php
return [
    'auth' => [
        'users.account.edit' => 'mypage',
        'users.account.withdrawal' => 'mypage',
        'users.account.password_reset' => 'mypage',
        'inquiry.index' => 'mypage',
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
                'title' => '비밀번호 변경',
            ],
            'users.account.withdrawal' => [
                'title' => '회원 탈퇴 ',
            ],
            'inquiry.index' => [
                'title' => '나의 문의 내역',
            ],
        ],
    ],
];
