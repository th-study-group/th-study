<?php

use App\Http\Controllers\Mcp\McpApiController;
use App\Http\Controllers\Mcp\McpJwtAuthController;
use App\Http\Controllers\Mcp\McpOAuthController;
use App\Http\Controllers\Mcp\Tools\BlogSearchToolController;
use App\Http\Controllers\Mcp\Tools\NoteController;
use Illuminate\Support\Facades\Route;

Route::post('/oauth/token', [McpOAuthController::class, 'token'])
    ->name('mcp.oauth.token');

Route::post('/login', [McpJwtAuthController::class, 'login'])
     ->name('mcp.jwt.login');

Route::post('/refresh', [McpJwtAuthController::class, 'refresh'])
     ->name('mcp.jwt.refresh');

Route::middleware(['auth.mcp.jwt'])->group(function () {
    Route::match(['GET', 'POST'], '/', [McpApiController::class, 'handle'])
        ->name('mcp.handle');

    Route::post('/tools/blog-search', [BlogSearchToolController::class, 'handle'])
        ->name('mcp.tools.blog-search'); 
    
    Route::post('/tools/notes', [NoteController::class, 'index'])
        ->name('mcp.tools.note.index'); 
});