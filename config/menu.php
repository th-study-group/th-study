<?php
return [
    'auth' => [
        'users.account.edit' => 'mypage',
        'users.account.withdrawal' => 'mypage',
        'users.account.password_reset' => 'mypage',
        'inquiries.index' => 'mypage',
        'inquiries.create' => 'mypage',
        'inquiries.edit' => 'mypage',
        'inquiries.show' => 'mypage',
        'admins.members.index' => 'admin',
        'admins.members.edit' => 'admin',
        'admins.inquiries.index' => 'admin',
        'admins.inquiries.create' => 'admin',
        'admins.inquiries.edit' => 'admin',
        'admins.inquiries.show' => 'admin',
        'admins.posts.index' => 'admin',
        'admins.posts.create' => 'admin',
        'admins.posts.edit' => 'admin',
        'admins.posts.show' => 'admin',
        'admins.guest_posts.index' => 'admin',
        'admins.guest_posts.edit' => 'admin',
        'admins.traffics.index' => 'admin',
    ],

    'menus' => [
        'mypage' => [
            'users.account.edit' => [
                'title' => '내 정보 변경',
            ],
            'users.account.password_reset' => [
                'title' => '비밀번호 변경',
            ],
            'users.account.withdrawal' => [
                'title' => '회원 탈퇴 ',
                'level' => 'normal',
            ],
            'inquiries.index' => [
                'title' => '나의 문의 내역',
            ],
        ],
        'admin' => [
            'admins.posts.index' => [
                'title' => '공지사항',
                'params' => [
                    'post_type' => 'notice',
                ],
            ],
            'admins.members.index' => [
                'title' => '회원 현황',
            ],
            'admins.inquiries.index' => [
                'title' => '문의내역'
            ],
            'admins.guest_posts.index' => [
                'title' => '홈페이지 문의내역',
                'params' => [
                    'post_type' => 'inquiries',
                ],
            ],
            'admins.traffics.index' => [
                'title' => '일일 유입 현황',
            ],
        ],
    ],
];
