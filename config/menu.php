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
                //'param_key' => 'idx', // 라우터 이름 할 때 파라미터 넘겨야함 
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
