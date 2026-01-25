<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::post('/send', [ChatController::class, 'send']);
Route::view('/chat', 'chat');

// TestController
Route::prefix("tests")->name("test.")->group(function() {
    Route::get("mail_queue", [TestController::class, 'mailQueue'])->name('mail_queue');
});

Route::get('/phpinfo', function(){
    phpinfo();
});