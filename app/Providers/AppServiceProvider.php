<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('custompaginate.pagination.bootstrap-5');
        Paginator::defaultSimpleView('custompaginate.pagination.simple-bootstrap-5');

        View::replaceNamespace('pagination', resource_path('views/custompaginate/pagination'));
    }
}
