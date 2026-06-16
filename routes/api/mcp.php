<?php

use App\Http\Controllers\Mcp\McpApiController;
use App\Http\Controllers\Mcp\McpJwtAuthController;
use App\Http\Controllers\Mcp\McpOAuthController;
use App\Http\Controllers\Mcp\Tools\BlogSearchToolController;
use App\Http\Controllers\Mcp\Tools\NoteCategoriesController;
use App\Http\Controllers\Mcp\Tools\NoteController;
use App\Http\Controllers\Mcp\Tools\NoteGroupController;
use App\Http\Controllers\Mcp\Tools\NoteTagController;
use App\Http\Controllers\Mcp\Tools\NoteTopicController;
use App\Http\Controllers\Mcp\Tools\TrafficLogController;
use App\Http\Controllers\Mcp\Tools\UserController;
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
        
    Route::post('/tools/note-groups', [NoteGroupController::class, 'index'])
        ->name('mcp.tools.note-group.index');

    Route::post('/tools/note-categories', [NoteCategoriesController::class, 'index'])
        ->name('mcp.tools.note-category.index');

    Route::post('/tools/note-topics', [NoteTopicController::class, 'index'])
        ->name('mcp.tools.note-topic.index');

    Route::post('/tools/note-tags', [NoteTagController::class, 'index'])
        ->name('mcp.tools.note-tag.index');

    Route::post('/tools/users', [UserController::class, 'index'])
        ->name('mcp.tools.user.index');

    Route::post('/tools/access-logs', [TrafficLogController::class, 'getAccessLogs'])
        ->name('mcp.tools.traffic.access-log');

    Route::post('/tools/bot-access-logs', [TrafficLogController::class, 'getBotAccessLogs'])
        ->name('mcp.tools.traffic.bot-access-log');
});
