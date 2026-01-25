<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard'); // 마이페이지 (로그인 후 진입페이지)   

Route::prefix('users')->name('users.')->group(function() {
    Route::get("/", [UserController::class, 'index'])->name('index'); 
    Route::get("/show", [UserController::class, 'show'])->name('account.show');
    Route::get("/edit", [UserController::class, 'edit'])->name("account.edit"); 
    Route::put("/update", [UserController::class, 'update'])->name('account.update');
    Route::get("/withdrawal", [UserController::class, 'withdrawal'])->name('account.withdrawal');
    Route::delete("/destroy", [UserController::class, 'destroy'])->name('account.soft.delete');
    Route::get("/password-reset", [UserController::class, 'passwordReset'])->name('account.password_reset');
});

Route::prefix('inquiries')->name('inquiries.')->group(function () {
    Route::get('/', [InquiryController::class, 'index'])->name('index');
    Route::get('/create', [InquiryController::class, 'create'])->name('create');
    Route::post('/', [InquiryController::class, 'store'])->name('store');
    Route::get('/show', [InquiryController::class, 'show'])->name('show');
    Route::get('/edit', [InquiryController::class, 'edit'])->name('edit');
    Route::put('/', [InquiryController::class, 'update'])->name('update');
    Route::delete('/{idx}', [InquiryController::class, 'destroy'])->name('soft.delete');
});