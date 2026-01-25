<?php

$status = [
    'wait' => '대기중',
    'in_progress' => '진행중',
    'on_hold' => '보류중',
    'completed' => '완료'
];

$postTypes = [
    'notice' => '공지사항',
    'free' => '잡담',
    'my_question' => '1:1문의',
    'support' => '지원',
    'inquiries' => '문의사항',
];

// 관리자, 회원 등 권한이 필요할 때 적용
$postTypeExcluded = ['inquiries'];

return [
    'status' => $status,
    'post_type' => $postTypes,
    'post_type_excluded' => $postTypeExcluded,
    'post_type_for_route' => array_values(
        array_diff(array_keys($postTypes), $postTypeExcluded)
    ),
];
