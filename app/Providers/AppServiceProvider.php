<?php

namespace App\Providers;

// use App\Models\Protocol;
// use App\Observers\ProtocolObserver;
// use Filament\Auth\Http\Responses\LoginResponse;
// use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Http\Responses\LoginResponse;
use Illuminate\Support\ServiceProvider;
use App\Http\Responses\LoginResponse as CustomLoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        // Bind interface LoginResponse ke class custom buatan kita
        $this->app->singleton(
            LoginResponseContract::class, // Interface Bawaan
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
