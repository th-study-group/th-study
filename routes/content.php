<?php

use App\Http\Controllers\NoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$noteGroups = config('note.group', []);

foreach (array_keys($noteGroups) as $group) {
    Route::prefix($group)->name($group . '.')->group(function () use ($group) {
        if ($group === 'blogs') {
            Route::get('/{legacySlug}/{rest?}', function (Request $request, string $legacySlug, ?string $rest = null) use ($group) {
                $targetPath = '/' . $group . '/life' . ($rest !== null && $rest !== '' ? '/' . ltrim($rest, '/') : '');
                $query = $request->getQueryString();

                if ($query !== null && $query !== '') {
                    $targetPath .= '?' . $query;
                }

                return redirect($targetPath, 301);
            })
                ->where('legacySlug', 'cafe|food|tour|shopping')
                ->where('rest', '.*');
        }

        Route::get('/create', [NoteController::class, 'create'])->name('create.blank')->defaults('group', $group);
        Route::post('/create', [NoteController::class, 'store'])->name('store.blank')->defaults('group', $group);
        Route::get('/{slug}/create', [NoteController::class, 'create'])->name('create')->defaults('group', $group);
        Route::post("/{slug}", [NoteController::class, 'store'])->name('store')->defaults('group', $group);
        Route::get('/{slug}/{idx}/show', [NoteController::class, 'show'])->name('show')->defaults('group', $group);
        Route::get('/{slug}/{idx}/edit', [NoteController::class, 'edit'])->name('edit')->defaults('group', $group);
        Route::put("/{slug}/{idx}", [NoteController::class, 'update'])->name('update')->defaults('group', $group);
        Route::delete("/{slug}/{idx}", [NoteController::class, 'destroy'])->name('soft.delete')->defaults('group', $group);
        Route::patch("/{slug}/{idx}/use-flag", [NoteController::class, 'updateUseFlag'])->name('use_flag.update')->defaults('group', $group);
        Route::patch("/{slug}/{idx}/thumbnail", [NoteController::class, 'destroyThumbnail'])->name('thumbnail.destroy')->defaults('group', $group);
        Route::delete("/{slug}/{idx}/tags", [NoteController::class, 'destroyTag'])->name('tags.destroy')->defaults('group', $group);
        Route::get('/{slug?}', [NoteController::class, 'index'])
            ->name('index')
            ->defaults('group', $group)
            ->defaults('showSide', true);
    });
}
