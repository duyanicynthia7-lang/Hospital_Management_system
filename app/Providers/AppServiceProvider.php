<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Render pagination links with Bootstrap 5 markup
        Paginator::useBootstrapFive();

        // Avoid "key too long" errors on older MySQL / MariaDB versions
        Schema::defaultStringLength(191);
    }
}
