<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\McpLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class McpOAuthController extends Controller
{
    public function auth(Request $request)
    {
        Log::channel('mcp')->info('MCP OAuth authorize entered', [
            'client_id' => $request->query('client_id'),
            'redirect_uri' => $request->query('redirect_uri'),
            'response_type' => $request->query('response_type'),
            'state_exists' => $request->filled('state'),
            'code_challenge_exists' => $request->filled('code_challenge'),
        ]);

        $validator = Validator::make($request->query(), [
            'response_type' => ['required', 'string', 'in:code'],
            'client_id' => ['required', 'string'],
            'redirect_uri' => ['required', 'string'],
            'state' => ['nullable', 'string'],
            'code_challenge' => ['nullable', 'string', 'min:43', 'max:128'],
            'code_challenge_method' => ['nullable', 'string', 'required_with:code_challenge', 'in:S256'],
        ]);

        if ($validator->fails()) {
            Log::channel('mcp')->warning('MCP OAuth authorize validation failed', [
                'errors' => $validator->errors()->toArray(),
                'client_id' => $request->query('client_id'),
                'redirect_uri' => $request->query('redirect_uri'),
            ]);

            return response()->json([
                'message' => 'Invalid authorization request.',
                'errors' => $validator->errors(),
            ], 400, $this->noStoreHeaders());
        }

        $validated = $validator->validated();

        if ($validated['client_id'] !== config('mcp.oauth.client_id')) {
            Log::channel('mcp')->warning('MCP OAuth invalid client_id', [
                'client_id' => $validated['client_id'],
            ]);

            return response('Invalid client_id.', 400);
        }

        if (!$this->isAllowedRedirectUri($validated['redirect_uri'])) {
            Log::channel('mcp')->warning('MCP OAuth invalid redirect_uri', [
                'redirect_uri' => $validated['redirect_uri'],
            ]);

            return response('Invalid redirect_uri.', 400);
        }

        return view('mcp.chatgpt.login', [
            'client_id' => $validated['client_id'],
            'redirect_uri' => $validated['redirect_uri'],
            'state' => $validated['state'] ?? null,
            'code_challenge' => $validated['code_challenge'] ?? null,
            'code_challenge_method' => $validated['code_challenge_method'] ?? null,
        ]);
    }

    public function login(McpLoginRequest $request)
    {
        $validated = $request->validated();
        $mcpGuard = Auth::guard('mcp_jwt');
        $candidateUser = User::where('email', $validated['email'])->first();

        Log::info('MCP OAuth login entered', [
            'email' => $validated['email'],
            'client_id' => $validated['client_id'],
            'redirect_uri' => $validated['redirect_uri'],
            'guard' => 'mcp_jwt',
            'candidate_user_idx' => $candidateUser?->idx,
            'password_length' => mb_strlen((string) $validated['password']),
            'state_exists' => !empty($validated['state']),
            'code_challenge_exists' => !empty($validated['code_challenge']),
        ]);

        if ($validated['client_id'] !== config('mcp.oauth.client_id')) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'OAuth 클라이언트 정보가 올바르지 않습니다.',
                ]);
        }

        if (!$this->isAllowedRedirectUri($validated['redirect_uri'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => '리디렉션 주소가 허용되지 않았습니다.',
                ]);
        }

        $mcpToken = $mcpGuard->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (!$mcpToken) {
            Log::channel('mcp')->warning('MCP OAuth login failed', [
                'email' => $validated['email'],
                'guard' => 'mcp_jwt',
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => '이메일 또는 비밀번호가 올바르지 않습니다.',
                ]);
        }

        $user = $mcpGuard->user();

        if (!$user instanceof User) {
            Log::channel('mcp')->warning('MCP OAuth user type invalid', [
                'guard' => 'mcp_jwt',
                'candidate_user_idx' => $candidateUser?->idx,
                'token_generated' => true,
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => '로그인 사용자 정보를 확인할 수 없습니다.',
                ]);
        }

        if (!$user->canAccessMcp()) {
            Log::channel('mcp')->warning('MCP OAuth login blocked', [
                'user_idx' => $user->getAuthIdentifier(),
                'guard' => 'mcp_jwt',
                'reason' => $user->mcpBlockedReason(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => $user->mcpBlockedReason(),
                ]);
        }

        $code = Str::random(80);

        Cache::put(
            $this->authorizationCodeCacheKey($code),
            [
                'user_id' => $user->getAuthIdentifier(),
                'client_id' => $validated['client_id'],
                'redirect_uri' => $validated['redirect_uri'],
                'code_challenge' => $validated['code_challenge'] ?? null,
                'code_challenge_method' => $validated['code_challenge_method'] ?? null,
            ],
            now()->addMinutes((int) config('mcp.oauth.code_ttl', 5))
        );

        Log::info('MCP OAuth authorize success', [
            'user_idx' => $user->getAuthIdentifier(),
            'client_id' => $validated['client_id'],
            'redirect_uri' => $validated['redirect_uri'],
            'code_prefix' => substr($code, 0, 8),
        ]);

        return redirect()->away($this->buildRedirectUrl($validated['redirect_uri'], [
            'code' => $code,
            'state' => $validated['state'] ?? null,
        ]));
    }

    public function token(Request $request): JsonResponse
    {
        Log::info('MCP OAuth token entered', [
            'grant_type' => $request->input('grant_type'),
            'client_id' => $request->input('client_id'),
            'code_exists' => $request->filled('code'),
            'refresh_token_exists' => $request->filled('refresh_token'),
        ]);

        $request->validate([
            'grant_type' => ['required', 'string'],
            'client_id' => ['required', 'string'],
            'client_secret' => ['required', 'string'],
        ]);

        if ($request->input('client_id') !== config('mcp.oauth.client_id')) {
            Log::channel('mcp')->warning('MCP OAuth token invalid client_id', [
                'client_id' => $request->input('client_id'),
            ]);

            return $this->jsonOAuthError('invalid_client', 401);
        }

        if ($request->input('client_secret') !== config('mcp.oauth.client_secret')) {
            Log::channel('mcp')->warning('MCP OAuth token invalid client_secret', [
                'client_id' => $request->input('client_id'),
            ]);

            return $this->jsonOAuthError('invalid_client', 401);
        }

        return match ($request->input('grant_type')) {
            'authorization_code' => $this->issueTokenByCode($request),
            'refresh_token' => $this->issueTokenByRefreshToken($request),
            default => $this->unsupportedGrantTypeResponse($request),
        };
    }

    private function issueTokenByCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $codeData = Cache::pull($this->authorizationCodeCacheKey($validated['code']));

        if (!$codeData) {
            Log::channel('mcp')->warning('MCP OAuth invalid authorization code');

            return $this->jsonOAuthError('invalid_grant', 400);
        }

        if (($codeData['client_id'] ?? null) !== $request->input('client_id')) {
            Log::channel('mcp')->warning('MCP OAuth authorization code client mismatch', [
                'cached_client_id' => $codeData['client_id'] ?? null,
                'request_client_id' => $request->input('client_id'),
            ]);

            return $this->jsonOAuthError('invalid_grant', 400);
        }

        $user = User::find($codeData['user_id'] ?? null);

        if (!$user || !$user->canAccessMcp()) {
            Log::channel('mcp')->warning('MCP OAuth token access denied', [
                'user_id' => $codeData['user_id'] ?? null,
            ]);

            return $this->jsonOAuthError('access_denied', 403);
        }

        JWTAuth::factory()->setTTL((int) config('jwt.ttl', 30));

        $accessToken = JWTAuth::claims([
            'token_type' => 'access',
        ])->fromUser($user);

        JWTAuth::factory()->setTTL((int) config('jwt.refresh_ttl', 20160));

        $refreshToken = JWTAuth::claims([
            'token_type' => 'refresh',
        ])->fromUser($user);

        JWTAuth::factory()->setTTL((int) config('jwt.ttl', 30));

        Log::channel('mcp')->info('MCP OAuth token issued', [
            'user_id' => $user->getKey(),
            'client_id' => $codeData['client_id'] ?? null,
            'expires_in' => (int) config('jwt.ttl', 30) * 60,
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => (int) config('jwt.ttl', 30) * 60,
        ], 200, $this->noStoreHeaders());
    }

    private function issueTokenByRefreshToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        try {
            $refreshToken = $validated['refresh_token'];

            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            if (($payload->get('token_type') ?? '') !== 'refresh') {
                Log::channel('mcp')->warning('MCP OAuth refresh invalid token type', [
                    'token_type' => $payload->get('token_type'),
                ]);

                return $this->jsonOAuthError('invalid_grant', 401);
            }

            $user = JWTAuth::setToken($refreshToken)->authenticate();

            if (!$user || !$user->canAccessMcp()) {
                Log::channel('mcp')->warning('MCP OAuth refresh access denied', [
                    'user_id' => $user?->getKey(),
                ]);

                return $this->jsonOAuthError('access_denied', 403);
            }

            JWTAuth::factory()->setTTL((int) config('jwt.ttl', 30));

            $accessToken = JWTAuth::claims([
                'token_type' => 'access',
            ])->fromUser($user);

            Log::channel('mcp')->info('MCP OAuth access token refreshed', [
                'user_id' => $user->getKey(),
                'expires_in' => (int) config('jwt.ttl', 30) * 60,
            ]);

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => (int) config('jwt.ttl', 30) * 60,
            ], 200, $this->noStoreHeaders());
        } catch (\Throwable $e) {
            Log::channel('mcp')->warning('MCP OAuth refresh failed', [
                'message' => $e->getMessage(),
            ]);

            return $this->jsonOAuthError('invalid_grant', 401);
        }
    }

    private function unsupportedGrantTypeResponse(Request $request): JsonResponse
    {
        Log::channel('mcp')->warning('MCP OAuth unsupported grant_type', [
            'grant_type' => $request->input('grant_type'),
        ]);

        return $this->jsonOAuthError('unsupported_grant_type', 400);
    }

    private function authorizationCodeCacheKey(string $code): string
    {
        return 'mcp_oauth_code:' . $code;
    }

    private function buildRedirectUrl(string $redirectUri, array $query): string
    {
        $filteredQuery = array_filter($query, static fn ($value) => $value !== null && $value !== '');
        $separator = str_contains($redirectUri, '?') ? '&' : '?';

        return $redirectUri . $separator . http_build_query($filteredQuery);
    }

    private function isAllowedRedirectUri(string $redirectUri): bool
    {
        if (!filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($redirectUri);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme === '' || $host === '') {
            return false;
        }

        $isLocalHost = in_array($host, ['localhost', '127.0.0.1'], true);

        if (!$isLocalHost && $scheme !== 'https') {
            return false;
        }

        if ($isLocalHost && !in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $allowedRedirectUris = config('mcp.oauth.redirect_uris', []);

        if ($allowedRedirectUris === []) {
            return true;
        }

        return in_array($redirectUri, $allowedRedirectUris, true);
    }

    private function jsonOAuthError(string $error, int $status): JsonResponse
    {
        return response()->json([
            'error' => $error,
        ], $status, $this->noStoreHeaders());
    }

    private function noStoreHeaders(): array
    {
        return [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ];
    }
}
