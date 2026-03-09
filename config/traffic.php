<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Access Log Excluded IPs
    |--------------------------------------------------------------------------
    |
    | 여기에 등록된 IP는 access_logs(사람 유입 로그)에 저장하지 않습니다.
    |
    */
    'access_log_excluded_ips' => [
        '52.78.167.218',
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversion Types
    |--------------------------------------------------------------------------
    |
    | 유입 전환 로그(conversion_logs.conversion_type)에 사용하는 표준 키/값입니다.
    |
    */
    'conversion_types' => [
        'page_view' => 'page_view',
        'signup' => 'signup',
        'login' => 'login',
        'purchase' => 'purchase',
        'click' => 'click',
        'outbound' => 'outbound',
        'download' => 'download',
        'share' => 'share',
        'search' => 'search',
    ],
];
