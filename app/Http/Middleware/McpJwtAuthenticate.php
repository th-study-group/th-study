<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class McpJwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        Log::channel('mcp')->info('MCP JWT middleware entered', [
            'method' => $request->method(),
            'path' => $request->path(),
            'bearer_exists' => $request->bearerToken() ? true : false,
        ]);

        $token = $request->bearerToken();

        if (!$token) {
            Log::channel('mcp')->warning('MCP authorization missing', [
                'path' => $request->path(),
            ]);

            return response()->json([
                'error' => 'authorization_required',
            ], 401, [
                'WWW-Authenticate' =>
                    'Bearer resource_metadata="' .
                    rtrim(config('app.url'), '/') .
                    '/.well-known/oauth-protected-resource"',
            ]);
        }

        try {
            $payload = JWTAuth::setToken($token)->getPayload();

            if (($payload->get('token_type') ?? '') !== 'access') {
                Log::channel('mcp')->warning('MCP invalid token type', [
                    'token_type' => $payload->get('token_type'),
                ]);

                return response()->json([
                    'error' => 'invalid_token',
                ], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user || !$user->canAccessMcp()) {
                Log::channel('mcp')->warning('MCP forbidden user', [
                    'user_id' => $user ? $user->idx : null,
                ]);

                return response()->json([
                    'error' => 'forbidden',
                    'message' => $user
                        ? $user->mcpBlockedReason()
                        : 'User not found.',
                ], 403);
            }

            Auth::setUser($user);

            Log::channel('mcp')->info('MCP JWT authenticated', [
                'user_id' => $user->idx,
            ]);

            return $next($request);
        } catch (\Throwable $e) {
            Log::channel('mcp')->warning('MCP JWT failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'invalid_token',
            ], 401);
        }
    }
}
