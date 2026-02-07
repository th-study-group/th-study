<?php

use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get("/register", [RegisterController::class, 'create'])->name('register.form')->defaults('hideSide', true); // 회원가입 폼
Route::post("/register", [RegisterController::class, 'register'])->name('register.submit'); // 회원가입 처리
Route::get("/login", [LoginController::class, 'login'])->name('login')->defaults('hideSide', true); // 로그인 폼
Route::post("/login", [LoginController::class, 'authenticate'])->name('authenticate'); // 로그인 처리 
Route::get("/logout", [LoginController::class, 'logout'])->name('logout'); // 로그아웃 처리

// 회원 가입 인증 링크
Route::get('/email/verify', [
    EmailVerifyController::class,'verify'
    ])->name('email.verify');

// 회원가입 인증 메일 재발송 (로그인상태)
Route::post('/email/resend', [EmailVerifyController::class,'resend'])
    ->middleware('auth')
    ->name('email.resend');

// 관리자 or 시더 생성 로그인 계정에 대한 비밀번호 재설정 
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [PasswordChangeController::class,'index'])
        ->name('password.change.form');
        
    Route::post('/password/change', [PasswordChangeController::class,'requirePasswordReset'])
        ->name('password.change.update');
});

// 계정찾기 화면
Route::get('/find-account', [ForgotPasswordController::class,'index'])
    ->name('password.find.account')
    ->defaults('hideSide', true);

// 비밀번호 초기화 메일 발송 (쓰로틀: 1분 5회)
Route::post('/find-password', [ForgotPasswordController::class,'requestAccountEmail'])
    ->middleware('throttle:5,1')
    ->name('password.request.account_email');

// 비밀번호 재설정 메일 링크 클릭
Route::get('/reset-password/{token}', [ResetPasswordController::class,'index'])
   ->name('password.reset.mail');

// 비밀번호 재설정처리
Route::post('/reset-password', [ResetPasswordController::class,'store'])
    ->name('password.reset.mail_update');