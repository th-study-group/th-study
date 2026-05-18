<?php
    return [
        'oauth' => [
            'client_id' => env('OAUTH_CLIENT_ID', 'thstudy-chatgpt'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'code_ttl' => (int) env('OAUTH_CODE_TTL', 5),
            'redirect_uris' => array_values(array_filter(array_map(
                static fn (string $uri) => trim($uri),
                explode(',', (string) env('OAUTH_REDIRECT_URIS', ''))
            ))),
        ],
        'tool_path' => base_path('mcp/tool.json'),
    ];
