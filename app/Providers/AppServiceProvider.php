<?php

namespace App\Providers;

use App\Models\Protocol;
use App\Observers\ProtocolObserver;
use BezhanSalleh\PanelSwitch\PanelSwitch;
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
        // Protocol::observe(ProtocolObserver::class);
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
        $panelSwitch
            // 1. Tentukan Panel apa saja yang muncul (opsional, jika ingin membatasi)
            // ->panels(['admin', 'reviewer', 'user'])

            // 2. Ubah tampilan menjadi Dropdown sederhana (defaultnya Modal)
            ->slideOver()

            // 3. Pasang Ikon untuk masing-masing Panel (Gunakan ID Panel)
            ->icons([
                'admin'    => 'heroicon-o-square-2-stack',
                'reviewer' => 'heroicon-o-clipboard-document-check',
                'user'     => 'heroicon-o-user',
            ])

            // 4. Atur Label (jika nama panel ID tidak rapi)
            ->labels([
                'admin'    => 'Admin Dashboard',
                'reviewer' => 'Reviewer Area',
                'user'     => 'My Account',
            ])

            // 5. Logika Siapa yang boleh melihat menu switch ini
            ->visible(fn (): bool => auth()->user()?->hasRole(['super_admin', 'reviewer']))

            // 6. Posisi menu switch (opsional)
            ->renderHook('panels::global-search.after');
    });
    }
}
