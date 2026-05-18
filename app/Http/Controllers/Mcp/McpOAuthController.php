<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcp\McpLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 챗지피티 oauth인증
 */
class McpOAuthController extends Controller
{
    /**
     * MCP OAuth 인증 요청 화면 : 챗지피티 
     *
     * @param Request $request
     * 
     * @return void
     */
    public function auth(Request $request)
    {
        Log::info('MCP OAuth authorize entered', [
            'client_id' => $request->query('client_id'),
            'redirect_uri' => $request->query('redirect_uri'),
            'response_type' => $request->query('response_type'),
            'state_exists' => $request->query('state') ? true : false,
        ]);

        if ($request->query('response_type') !== 'code') {
            return response('Invalid response_type.', 400);
        }

        if ($request->query('client_id') !== config('mcp.oauth.client_id')) {
            return response('Invalid client_id.', 400);
        }

        return view('mcp.chatgpt.login', [
            'client_id' => $request->query('client_id'),
            'redirect_uri' => $request->query('redirect_uri'),
            'state' => $request->query('state'),
            'code_challenge' => $request->query('code_challenge'),
            'code_challenge_method' => $request->query('code_challenge_method'),
        ]);
    }

    /**
     * Mcp oAuth 로그인 처리 : 챗지피티
     *
     * @param Request $request
     * @return void
     */
    public function login(McpLoginRequest $request)
    {
        $validated = $request->validated();
        $mcpGuard = Auth::guard('mcp_jwt');
        $candidateUser = User::where('email', $validated['email'])->first();
        $mcpToken = false;

        Log::info('MCP OAuth login entered', [
            'email' => $validated['email'],
            'client_id' => $validated['client_id'],
            'redirect_uri' => $validated['redirect_uri'],
            'guard' => 'mcp_jwt',
            'candidate_user_idx' => $candidateUser?->idx,
            'password_length' => mb_strlen((string) $validated['password']),
        ]);

        if ($validated['client_id'] !== config('mcp.oauth.client_id')) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'OAuth 클라이언트 정보가 올바르지 않습니다.',
                ]);
        }

        $mcpToken = $mcpGuard->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (!$mcpToken) {
            Log::warning('MCP OAuth login failed', [
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
            Log::warning('MCP OAuth user type invalid', [
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
            Log::warning('MCP OAuth login blocked', [
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
            'mcp_oauth_code:' . $code,
            [
                'user_id' => $user->getAuthIdentifier(),
                'client_id' => $validated['client_id'],
                'redirect_uri' => $validated['redirect_uri'],
                'code_challenge' => $validated['code_challenge'] ?? null,
                'code_challenge_method' => $validated['code_challenge_method'] ?? null,
            ],
            now()->addMinutes(config('mcp.oauth.code_ttl', 5))
        );

        Log::info('MCP OAuth authorize success', [
            'user_idx' => $user->getAuthIdentifier(),
            'client_id' => $validated['client_id'],
            'redirect_uri' => $validated['redirect_uri'],
            'code_prefix' => substr($code, 0, 8),
        ]);

        $query = [
            'code' => $code,
        ];

        if (!empty($validated['state'])) {
            $query['state'] = $validated['state'];
        }

        return redirect()->away(
            $validated['redirect_uri'] . '?' . http_build_query($query)
        );
    }
}
