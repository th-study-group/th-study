<?php
    return [
        'oauth' => [
            'client_id' => env('OAUTH_CLIENT_ID', 'thstudy-chatgpt'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'code_ttl' => (int) env('OAUTH_CODE_TTL', 5),
        ],
        'tool_path' => base_path('mcp/tool.json'),
    ];
