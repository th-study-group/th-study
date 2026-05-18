<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class McpJwtAuthenticate
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        Log::info('MCP auth middleware entered', [
            'method' => $request->method(),
            'path' => $request->path(),
            'bearer_token_exists' => $request->bearerToken() ? true : false,
            'ip' => $request->ip(),
        ]);

        try {

            $token = $request->bearerToken();

            if (!$token) {
                Log::warning('MCP authorization missing', [
                    'path' => $request->path(),
                ]);

                return $this->unauthorized();
            }

            $payload = JWTAuth::setToken($token)
                ->getPayload();

            if (($payload->get('token_type') ?? '') !== 'access') {
                Log::warning('MCP invalid token type', [
                    'token_type' => $payload->get('token_type'),
                ]);
                return response()->json([
                    'message' =>
                        'Access token required.'
                ], 401);
            }

            $user = JWTAuth::setToken($token)
                ->authenticate();

            if (!$user) {
                Log::warning('MCP user not found from token');

                return $this->unauthorized();
            }

            if (method_exists($user, 'canAccessMcp') && !$user->canAccessMcp()) {
                Log::warning('MCP user forbidden', [
                    'user_id' => $user->getKey(),
                    'reason' => $user->mcpBlockedReason(),
                ]);

                return response()->json([
                    'message' =>
                        $user->mcpBlockedReason(),
                ], 403);
            }

            auth()->setUser($user);

            Log::info('MCP auth success', [
                'user_id' => $user->getKey(),
            ]);

            return $next($request);

        } catch (Throwable $e) {
            Log::error('MCP auth failed', [
                'message' => $e->getMessage(),
            ]);

            return $this->unauthorized();
        }
    }

    private function unauthorized()
    {
        $appUrl = rtrim(config('app.url'), '/');

        return response()->json([
            'message' => 'Unauthorized',
        ], 401, [
            'WWW-Authenticate' =>
                'Bearer resource_metadata="' .
                $appUrl .
                '/.well-known/oauth-protected-resource"',
        ]);
    }
}
