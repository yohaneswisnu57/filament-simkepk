<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ReviewerPanelProvider;
use App\Providers\Filament\UserPanelProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    ReviewerPanelProvider::class,
    UserPanelProvider::class,
    TelescopeServiceProvider::class,
];
