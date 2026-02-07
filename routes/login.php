<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard')->defaults('hideSide', true); // 마이페이지 (로그인 후 진입페이지)   

Route::prefix('users')->name('users.')->group(function() {
    Route::get("/", [UserController::class, 'index'])->name('index'); 
    Route::get("/show", [UserController::class, 'show'])->name('account.show');
    Route::get("/edit", [UserController::class, 'edit'])->name("account.edit"); 
    Route::put("/update", [UserController::class, 'update'])->name('account.update');
    Route::get("/withdrawal", [UserController::class, 'withdrawal'])->name('account.withdrawal');
    Route::delete("/destroy", [UserController::class, 'destroy'])->name('account.soft.delete');
    Route::get("/password-reset", [UserController::class, 'passwordReset'])->name('account.password_reset');
    Route::patch("/password/change-request", [UserController::class, 'passwordResetRequest'])->name('account.password_reset.request');
});

Route::prefix('inquiries')->name('inquiries.')->group(function () {
    Route::get('/', [InquiryController::class, 'index'])->name('index');
    Route::get('/create', [InquiryController::class, 'create'])->name('create');
    Route::post('/', [InquiryController::class, 'store'])->name('store');
    Route::get('/{idx}/show', [InquiryController::class, 'show'])->name('show');
    Route::get('/{idx}/edit', [InquiryController::class, 'edit'])->name('edit');
    Route::put('/{idx}', [InquiryController::class, 'update'])->name('update');
    Route::delete('/{idx}', [InquiryController::class, 'destroy'])->name('soft.delete');
});

// 댓글 라우팅
Route::prefix("comments")->name("comments.")->group(function() {
    //Route::get("/show", [CommentController::class, 'show'])->name('show');
    Route::post("/", [CommentController::class, 'store'])->name('store');
    Route::put("/{idx}", [CommentController::class, 'update'])->name('update');
    Route::delete("/{idx}", [CommentController::class, 'destroy'])->name('soft.delete');
});
