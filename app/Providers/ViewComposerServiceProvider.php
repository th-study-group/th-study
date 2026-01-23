<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot()
    {
        View::composer('layouts.*', function ($view) {
            $routeName = optional(request()->route())->getName();
            $notes = config('note', []);
            $view->with('sideNotes', $notes[$routeName] ?? []);
        });
    }
}
