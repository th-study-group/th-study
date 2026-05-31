<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
            $route = request()->route();
            $routeName = $route?->getName();
            $noteGroup = $route?->parameter('group');

            $notes = config('note', []);
           
            $menuAuths = config('menu.auth', []);
            $menus = config('menu.menus', []);

            $sideMenuKey = $menuAuths[$routeName] ?? '';

            $userLevel = Auth::user()?->level ?? 'normal';

            $menuFlag = array_filter(
                $menus[$sideMenuKey] ?? [],
                function ($menu) use ($userLevel) {
                    if (!isset($menu['level'])) {
                        return true; // level 없으면 기본 노출
                    }
                    return $menu['level'] === $userLevel;
                }
            );
            
            $view->with([
                'sideNotes' => $notes[$noteGroup] ?? [],
                'sideMenuFlag' => $userLevel,
                'sideMenuAuth' => array_keys($menuAuths),
                'sideMenus' => $menuFlag,
                'accountIdx' => Auth::user()?->idx,
            ]);
        });

        View::composer('*', function ($view) {
            $view->with([
                'accountIdx' => Auth::id(),
            ]);
        });
    }
}
