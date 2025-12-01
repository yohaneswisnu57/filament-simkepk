<?php

namespace App\Providers;

use App\Models\Protocol;
use App\Observers\ProtocolObserver;
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
        //
        Protocol::observe(ProtocolObserver::class);
    }
}
