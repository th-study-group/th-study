<?php
return [
    'auth' => [
        'users.account.edit' => 'mypage',
        'mypage.inquiries' => 'mypage',
    ],

    'menus' => [
        'mypage' => [
            'users.account.edit' => [
                'title' => '내 정보 변경',
                //'param_key' => 'idx', // 라우터 이름 할 때 파라미터 넘겨야함 
            ],
        ],
    ],
];
