<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 메인화면
Route::get('/', function () {
    return view('home', [
        'postType' => 'inquiries'
    ]);
})->name('home')->defaults('hideSide', true);

// 소개페이지
Route::get('/intro', function () {
    return view('intro');
})->name('intro')->defaults('hideSide', true);

// sidebar 없는 정적 페이지들
Route::group([], function() {
    Route::view('/privacy-policy', 'privacy')
        ->name('privacy')
        ->defaults('hideSide', true);
    Route::view('/terms-of-service', 'terms')
        ->name('terms')
        ->defaults('hideSide', true);
});