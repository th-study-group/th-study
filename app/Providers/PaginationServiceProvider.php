<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class PaginationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->configurePaginator();
        $this->configureViews();
    }

    protected function configurePaginator(): void
    {
        if (config('pagination.use_bootstrap', true)) {
            Paginator::useBootstrapFive();
        }
    }

    protected function configureViews(): void
    {
        Paginator::defaultView(
            config('pagination.views.full', 'pagination.custom')
        );

        Paginator::defaultSimpleView(
            config('pagination.views.simple', 'pagination.simple')
        );
    }
}
