<?php

use Illuminate\Support\Facades\Route;

// 사진 라우팅
Route::prefix('photo')->name('photo.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('photos.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 영상 라우팅
Route::prefix('video')->name('video.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('videos.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 정보 라우팅
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('blogs.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 장소 라우팅
Route::prefix('map')->name('map.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('maps.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});

// 문서 라우팅
Route::prefix('document')->name('document.')->group(function () {
    Route::get('/{slug?}', function ($slug = null) {
        return view('documents.index');
    })->name('index')
      ->middleware('note.slug')
      ->defaults('showSide', true);
});