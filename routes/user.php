<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuestPostController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// 게시글 라우팅
Route::prefix("posts/{post_type}")
    ->name("posts.")
    ->whereIn('post_type', config('board.post_type_for_route'))
    ->group(function() {
        Route::get("/", [PostController::class, 'index'])->name('index');
        //Route::get("/create", [PostController::class, 'create'])->name('create');
        Route::get("/{idx}/show", [PostController::class, 'show'])->name('show');
        Route::post("/", [PostController::class, 'store'])->name('store');
        //Route::get("/{idx}/edit", [PostController::class, 'edit'])->name("edit");
        //Route::put("/{idx}", [PostController::class, 'update'])->name('update');
        //Route::delete("/{idx}", [PostController::class, 'destroy'])->name('soft.delete');
    });

// 댓글 라우팅
/*
Route::prefix("comments")->name("comments.")->group(function() {
    Route::get("/show", [CommentController::class, 'show'])->name('show');
    Route::post("/", [CommentController::class, 'store'])->name('store');
    Route::put("/{idx}", [CommentController::class, 'update'])->name('update');
    Route::delete("/{idx}", [CommentController::class, 'destroy'])->name('soft.delete');
});
*/

// 미인증 게시글 라우팅
Route::prefix("guest-posts/{post_type}")
    ->name("guest_posts.")
    ->whereIn('post_type', array_keys(config('board.post_type')))
    ->group(function() {
        Route::post("/", [GuestPostController::class, 'store'])->name('store');
});