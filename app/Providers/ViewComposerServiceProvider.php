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
           
            $menuAuths = config('menu.auth', []);
            $menus = config('menu.menus', []);

            $sideMenuKey = $menuAuths[$routeName] ?? '';
            
            $view->with([
                'sideNotes' => $notes[$routeName] ?? [],
                'sideMenuAuth' => array_keys($menuAuths),
                'sideMenus' => $menus[$sideMenuKey] ?? [],
                'accountIdx' => auth()->user()?->idx,
            ]);
        });

        View::composer('*', function ($view) {
            $view->with([
                'accountIdx' => auth()->id(),
            ]);
        });
    }
}