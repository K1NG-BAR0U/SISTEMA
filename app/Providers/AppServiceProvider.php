<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Farmacia;
use App\Observers\FarmaciaObserver;

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
        Farmacia::observe(FarmaciaObserver::class);
    }
}
