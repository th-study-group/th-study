<?php

use App\Http\Controllers\Admins\CommentController;
use App\Http\Controllers\Admins\InquiryController;
use App\Http\Controllers\Admins\MemberController;
use App\Http\Controllers\Admins\PostController;
use Illuminate\Support\Facades\Route;

// 회원라우팅 
Route::prefix('members')->name('members.')->group(function () {
    Route::get("/", [MemberController::class, 'index'])->name('index'); 
    Route::get("/{idx}/show", [MemberController::class, 'show'])->name('show');
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
    Route::delete("/{idx}", [PostController::class, 'destroy'])->name('soft.delete');
});

// 문의내역 라우팅 
Route::prefix('inquiries')->name('inquiries.')->group(function () {
    Route::get("/", [InquiryController::class, 'index'])->name('index'); 
    Route::get("/{idx}/show", [InquiryController::class, 'show'])->name('show');
    Route::patch("/{idx}/status", [InquiryController::class, 'updateStatus'])->name('status.update');
    Route::delete("/{idx}", [InquiryController::class, 'destroy'])->name('soft.delete');
});

// 댓글 라우팅
Route::prefix("comments")->name("comments.")->group(function() {
    Route::get("/show", [CommentController::class, 'show'])->name('show');
    Route::post("/", [CommentController::class, 'store'])->name('store');
    Route::put("/{idx}", [CommentController::class, 'update'])->name('update');
    Route::delete("/{idx}", [CommentController::class, 'destroy'])->name('soft.delete');
});