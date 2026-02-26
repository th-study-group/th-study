<?php

$status = [
    'wait' => '대기',
    'in_progress' => '진행',
    'on_hold' => '보류',
    'completed' => '완료'
];

$statusBadgeClasses = [
    'wait' => 'secondary',
    'in_progress' => 'warning',
    'on_hold' => 'dark',
    'completed' => 'success',
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

// 미노출이 기본값 설정되어야 하는 경우 
$forceUseFlagSecretTypes = ['notice'];

return [
    'status' => $status,
    'status_badge_classes' => $statusBadgeClasses,
    'post_type' => $postTypes,
    'post_type_excluded' => $postTypeExcluded,
    'post_type_inquiry' => 'inquiries',
    'post_type_for_route' => array_values(
        array_diff(array_keys($postTypes), $postTypeExcluded)
    ),
    'force_use_flag_secret_type' => array_values(
        array_diff(array_keys($postTypes), $forceUseFlagSecretTypes)
    ),
];
