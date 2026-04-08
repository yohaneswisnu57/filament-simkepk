<?php

namespace App\Providers;

// use App\Models\Protocol;
// use App\Observers\ProtocolObserver;
// use Filament\Auth\Http\Responses\LoginResponse;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        // Bind interface LoginResponse ke class custom buatan kita
        $this->app->singleton(
            LoginResponseContract::class,
            CustomLoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->visible(fn () => false);
        });
    }
}
