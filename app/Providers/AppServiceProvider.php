<?php

namespace App\Providers;

use App\Repositories\Contracts\IPharmacy;
use App\Repositories\Decorators\CachePharmacy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IPharmacy::class, CachePharmacy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Pharmacy::observe(\App\Observers\PharmacyObserver::class);
    }
}
