<?php

use App\Http\Controllers\Mcp\McpOAuthController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('mcp')->group(function () {
    Route::get('/oauth/authorize',[McpOAuthController::class, 'auth'])->name('mcp.oauth.authorize');
    Route::post('/oauth/login', [McpOAuthController::class, 'login'])->name('mcp.oauth.login');
    Route::get('/oauth-test', function (Request $request) {
        abort_unless(app()->environment('local'), 404);

        Log::info('MCP oauth callback', [
            'code_exists' => $request->query('code') ? true : false,
        ]);

        return response()->json([
            'code' => $request->query('code'),
            'state' => $request->query('state'),
            'all' => $request->query(),
        ]);
    });
});


Route::get('/.well-known/oauth-protected-resource', function () {
    $appUrl = rtrim(config('app.url'), '/');

    Log::info('MCP well-known protected resource requested');

    return response()->json([
        'resource' => $appUrl . '/api/mcp',
        'authorization_servers' => [
            $appUrl,
        ],
        'scopes_supported' => [],
    ], 200, [
        'Content-Type' => 'application/json;charset=UTF-8',
        'Cache-Control' => 'no-store',
        'Pragma' => 'no-cache',
    ], JSON_UNESCAPED_SLASHES);
});

Route::get('/.well-known/oauth-authorization-server', function () {
    $appUrl = rtrim(config('app.url'), '/');

    Log::info('MCP well-known authorization server requested');

    return response()->json([
        'issuer' => $appUrl,
        'authorization_endpoint' => $appUrl . '/mcp/oauth/authorize',
        'token_endpoint' => $appUrl . '/api/mcp/oauth/token',
        'response_types_supported' => ['code'],
        'grant_types_supported' => ['authorization_code', 'refresh_token'],
        'token_endpoint_auth_methods_supported' => ['client_secret_post'],
        'code_challenge_methods_supported' => ['S256'],
        'scopes_supported' => [],
    ], 200, [
        'Content-Type' => 'application/json;charset=UTF-8',
        'Cache-Control' => 'no-store',
        'Pragma' => 'no-cache',
    ], JSON_UNESCAPED_SLASHES);
});

Route::get('/.well-known/openai-apps-challenge', function () {
    return response('GvRAFJ71Z9aJCH3rV_6OPN2XUB7JgAvhkscbjDvbMWk', 200)
        ->header('Content-Type', 'text/plain');
});