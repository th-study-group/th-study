<?php

use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register.form')
        ->defaults('hideSide', true);

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.submit');

    Route::get('/login', [LoginController::class, 'login'])
        ->name('login')
        ->defaults('hideSide', true);

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('authenticate');
});

Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('/email/verify', [EmailVerifyController::class, 'verify'])
    ->name('email.verify');

Route::post('/email/resend', [EmailVerifyController::class, 'resend'])
    ->middleware('auth')
    ->name('email.resend');

Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'index'])
        ->name('password.change.form');

    Route::post('/password/change', [PasswordChangeController::class, 'requirePasswordReset'])
        ->name('password.change.update');
});

Route::get('/find-account', [ForgotPasswordController::class, 'index'])
    ->name('password.find.account')
    ->defaults('hideSide', true);

Route::post('/find-password', [ForgotPasswordController::class, 'requestAccountEmail'])
    ->middleware('throttle:5,1')
    ->name('password.request.account_email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'index'])
    ->name('password.reset.mail');

Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->name('password.reset.mail_update');
