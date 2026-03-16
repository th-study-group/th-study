<?php

use App\Http\Controllers\Admins\CommentController;
use App\Http\Controllers\Admins\GuestPostController;
use App\Http\Controllers\Admins\InquiryController;
use App\Http\Controllers\Admins\MemberController;
use App\Http\Controllers\Admins\PostController;
use App\Http\Controllers\Admins\TrafficController;
use App\Http\Controllers\PushController;
use Illuminate\Support\Facades\Route;

// 회원라우팅 
Route::prefix('members')->name('members.')->group(function () {
    Route::get("/", [MemberController::class, 'index'])->name('index'); 
    Route::get("/{idx}/edit", [MemberController::class, 'edit'])->name("edit");
    Route::put("/{idx}", [MemberController::class, 'update'])->name('update');
});

// 게시글 라우팅
Route::prefix('posts/{post_type}')
        ->name('posts.')
        ->whereIn('post_type', config('board.post_type_for_route'))
        ->group(function() {
    Route::get("/", [PostController::class, 'index'])->name('index'); 
    Route::get("/{idx}/show", [PostController::class, 'show'])->name('show');
    Route::get("/create", [PostController::class, 'create'])->name('create');
    Route::post("/", [PostController::class, 'store'])->name('store');
    Route::get("/{idx}/edit", [PostController::class, 'edit'])->name("edit");
    Route::put("/{idx}", [PostController::class, 'update'])->name('update');
    Route::patch("/{idx}/use-flag", [PostController::class, 'updateUseFlag'])->name('use_flag.update');
    Route::delete("/{idx}", [PostController::class, 'destroy'])->name('soft.delete');
});

// 문의내역 라우팅 
Route::prefix('inquiries')->name('inquiries.')->group(function () {
    Route::get("/", [InquiryController::class, 'index'])->name('index'); 
    Route::get("/{idx}/show", [InquiryController::class, 'show'])->name('show');
    Route::patch("/{idx}/status", [InquiryController::class, 'updateStatus'])->name('status.update');
    Route::delete("/{idx}", [InquiryController::class, 'destroy'])->name('soft.delete');
});

// 홈페이지 상담내역 라우팅 
Route::prefix("guest-posts/{post_type}")
    ->name("guest_posts.")
    ->whereIn('post_type', array_keys(config('board.post_type')))
    ->group(function() {
    Route::get("/", [GuestPostController::class, 'index'])->name('index'); 
    Route::get("/{idx}/edit", [GuestPostController::class, 'edit'])->name("edit");
    Route::put("/{idx}", [GuestPostController::class, 'update'])->name('update');
    Route::delete("/{idx}", [GuestPostController::class, 'destroy'])->name('soft.delete');
    
});

// 댓글 라우팅
Route::prefix("comments")->name("comments.")->group(function() {
    Route::get("/show", [CommentController::class, 'show'])->name('show');
    Route::post("/", [CommentController::class, 'store'])->name('store');
    Route::put("/{idx}", [CommentController::class, 'update'])->name('update');
    Route::delete("/{idx}", [CommentController::class, 'destroy'])->name('soft.delete');
});

// 웹 푸시 발송
Route::middleware(['throttle:5,1'])->prefix('push')->group(function () {
    Route::post('/send-to-user', [PushController::class, 'sendToUser'])->name('send.push'); // 발송(다중)
});

// 유입 전환 관리자
Route::prefix("traffics")->name("traffics.")->group(function() {
    Route::get("/", [TrafficController::class, 'index'])->name('index'); 
});

// readme.md 
Route::view('/readme.md', 'readme')->name('readme');
