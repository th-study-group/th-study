<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

$noteGroups = config('note.group', []);

foreach (array_keys($noteGroups) as $group) {
    Route::prefix($group)->name($group . '.')->group(function () use ($group) {
        Route::get('/{slug?}', [NoteController::class, 'index'])
            ->name('index')
            ->defaults('group', $group)
            ->defaults('showSide', true);
        Route::get('/{slug}/{idx}/show', [NoteController::class, 'show'])->name('show')->defaults('group', $group);
        Route::get('/{slug}/create', [NoteController::class, 'create'])->name('create')->defaults('group', $group);
        Route::post("/{slug}", [NoteController::class, 'store'])->name('store');
        Route::get('/{slug}/{idx}/edit', [NoteController::class, 'edit'])->name('edit')->defaults('group', $group);
        Route::put("/{slug}/{idx}", [NoteController::class, 'update'])->name('update');
        Route::delete("/{slug}/{idx}", [NoteController::class, 'destroy'])->name('soft.delete');
        Route::patch("/{slug}/{idx}/thumbnail", [NoteController::class, 'destoryThumbnail'])->name('thumbnail.destroy');
    });
}
