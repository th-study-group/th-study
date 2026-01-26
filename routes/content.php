<?php

use Illuminate\Support\Facades\Route;

// 사진 라우팅
Route::prefix('photos')->name('photos.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('photos.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 영상 라우팅
Route::prefix('videos')->name('videos.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('videos.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 정보 라우팅
Route::prefix('blogs')->name('blogs.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('blogs.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 장소 라우팅
Route::prefix('maps')->name('maps.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('maps.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 문서 라우팅
Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('documents.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});