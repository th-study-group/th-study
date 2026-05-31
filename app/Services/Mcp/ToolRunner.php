<?php

namespace App\Services\Mcp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ToolRunner
{
    public function run(?string $tool, array $args): array
    {
        if (!$tool) {
            return [
                'error' => 'Tool name required',
                'status' => 400,
            ];
        }

        $toolPath = config('mcp.tool_path');

        if (!$toolPath || !file_exists($toolPath)) {
            Log::channel('mcp')->warning('MCP tool file missing', [
                'tool_path' => $toolPath,
            ]);

            return [
                'error' => 'Tool file not found',
                'status' => 500,
            ];
        }

        $tools = json_decode(
            file_get_contents($toolPath),
            true
        );

        if (!is_array($tools)) {
            Log::channel('mcp')->warning('MCP tool file invalid json', [
                'tool_path' => $toolPath,
            ]);

            return [
                'error' => 'Invalid tool file',
                'status' => 500,
            ];
        }

        $toolInfo = collect($tools)->firstWhere('name', $tool);

        if (!$toolInfo) {
            Log::channel('mcp')->warning('MCP unknown tool', [
                'tool' => $tool,
            ]);

            return [
                'error' => 'Tool not found',
                'status' => 404,
            ];
        }

        $user = Auth::user();
        $userLevel = $user->level ?? null;
        $allowedLevels = $toolInfo['levels'] ?? [];

        if (!$userLevel || empty($allowedLevels) || !in_array($userLevel, $allowedLevels, true)) {
            Log::channel('mcp')->warning('MCP tool permission denied', [
                'tool' => $tool,
                'user_id' => $user->id ?? null,
                'user_level' => $userLevel,
                'allowed_levels' => $allowedLevels,
            ]);

            return [
                'error' => '이 도구를 사용할 권한이 없습니다.',
                'status' => 403,
            ];
        }

        $url = $toolInfo['url'] ?? null;

        if (!$url) {
            Log::channel('mcp')->warning('MCP tool url missing', [
                'tool' => $tool,
            ]);

            return [
                'error' => 'Tool url missing',
                'status' => 500,
            ];
        }

        $method = strtoupper($toolInfo['method'] ?? 'POST');

        Log::channel('mcp')->info('MCP tool dispatch started', [
            'tool' => $tool,
            'url' => $url,
            'method' => $method,
            'args_keys' => array_keys($args),
        ]);

        try {
            $subRequest = Request::create(
                $url,
                $method,
                $args,
                [],
                [],
                [
                    'HTTP_ACCEPT' => 'application/json',
                    'CONTENT_TYPE' => 'application/json',
                ]
            );

            if (request()->header('Authorization')) {
                $subRequest->headers->set(
                    'Authorization',
                    request()->header('Authorization')
                );
            }

            if (Auth::check()) {
                $subRequest->setUserResolver(function () {
                    return Auth::user();
                });
            }

            $response = app()->handle($subRequest);

            $content = $response->getContent();
            $decoded = json_decode($content, true);

            Log::channel('mcp')->info('MCP tool dispatch completed', [
                'tool' => $tool,
                'url' => $url,
                'status' => $response->getStatusCode(),
                'count' => $decoded['count'] ?? null,
            ]);

            if (!is_array($decoded)) {
                return [
                    'error' => 'Invalid tool response',
                    'status' => $response->getStatusCode(),
                    'raw' => $content,
                ];
            }

            return $decoded;
        } catch (\Throwable $e) {
            Log::channel('mcp')->error('MCP tool dispatch failed', [
                'tool' => $tool,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return [
                'error' => 'Tool execution failed',
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }
}