<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\McpJwtLoginRequest;
use App\Http\Requests\Mcp\McpJwtRefreshRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * JWT 로그인 컨틀롤러
 */
class McpJwtAuthController extends Controller
{
    /**
     * 로그인
     *
     * @param McpJwtLoginRequest $request
     * @return JsonResponse
     */
    public function login(McpJwtLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        Log::channel('mcp')->info('MCP direct JWT login entered', [
            'email' => $validated['email'],
        ]);

        $candidateUser = User::query()
            ->where('email', $validated['email'])
            ->whereNotNull('email_verify_datetime')
            ->first();

        if (!$candidateUser) {
            return response()->json([
                'message' => '이메일 인증이 완료된 계정만 로그인할 수 있습니다.',
            ], 403);
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        /** @var \PHPOpenSourceSaver\JWTAuth\JWTGuard $guard */
        $guard = Auth::guard('mcp_jwt');
        $accessToken = $guard
            ->claims([
                'token_type' => 'access',
            ])
            ->attempt($credentials);

        if (!$accessToken) {
            Log::channel('mcp')->warning('MCP direct JWT login failed', [
                'email' => $validated['email'],
            ]);

            return response()->json([
                'message' => '이메일 또는 비밀번호가 올바르지 않습니다.',
            ], 401);
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::guard('mcp_jwt')->user();

        if (!$user || !$user->canAccessMcp()) {
            Log::channel('mcp')->warning('MCP direct JWT login blocked', [
                'user_id' => $user ? $user->getAuthIdentifier() : null,
                'reason' => $user ? $user->mcpBlockedReason() : 'User not found.',
            ]);

            return response()->json([
                'message' => $user ? $user->mcpBlockedReason() : 'User not found.',
            ], 403);
        }

        JWTAuth::factory()->setTTL((int) config('jwt.refresh_ttl', 20160));

        $refreshToken = JWTAuth::claims([
            'token_type' => 'refresh',
        ])->fromUser($user);

        JWTAuth::factory()->setTTL((int) config('jwt.ttl', 30));

        Log::channel('mcp')->info('MCP direct JWT login issued', [
            'user_id' => $user->getAuthIdentifier(),
            'expires_in' => (int) config('jwt.ttl', 30) * 60,
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => (int) config('jwt.ttl', 30) * 60,
        ], 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }

    public function refresh(McpJwtRefreshRequest $request): JsonResponse
    {
        $validated = $request->validated();

        Log::channel('mcp')->info('MCP direct JWT refresh entered', [
            'refresh_token_exists' => !empty($validated['refresh_token']),
        ]);

        try {
            $refreshToken = $validated['refresh_token'];

            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if (($payload->get('token_type') ?? '') !== 'refresh') {
                Log::channel('mcp')->warning('MCP direct JWT refresh invalid token type', [
                    'token_type' => $payload->get('token_type'),
                ]);

                return response()->json([
                    'message' => '잘못된 refresh token입니다.',
                ], 401);
            }

            $user = JWTAuth::setToken($refreshToken)->authenticate();

            if (!$user || !$user->canAccessMcp()) {
                Log::channel('mcp')->warning('MCP direct JWT refresh blocked', [
                    'user_id' => $user ? $user->getAuthIdentifier() : null,
                    'reason' => $user ? $user->mcpBlockedReason() : 'User not found.',
                ]);

                return response()->json([
                    'message' => $user ? $user->mcpBlockedReason() : 'User not found.',
                ], 403);
            }

            JWTAuth::factory()->setTTL((int) config('jwt.ttl', 30));

            $newAccessToken = JWTAuth::claims([
                'token_type' => 'access',
            ])->fromUser($user);

            Log::channel('mcp')->info('MCP direct JWT refreshed', [
                'user_id' => $user->getAuthIdentifier(),
                'expires_in' => (int) config('jwt.ttl', 30) * 60,
            ]);

            return response()->json([
                'access_token' => $newAccessToken,
                'token_type' => 'Bearer',
                'expires_in' => (int) config('jwt.ttl', 30) * 60,
            ], 200, [
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Throwable $e) {
            Log::channel('mcp')->warning('MCP direct JWT refresh failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'refresh 실패',
            ], 401);
        }
    }
}
