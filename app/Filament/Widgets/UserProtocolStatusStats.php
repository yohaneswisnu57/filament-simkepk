<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserProtocolStatusStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        // Visible to roles: user, peneliti
        return Auth::user()->hasRole(['user', 'peneliti']);
    }

    protected function getStats(): array
    {
        $userId = Auth::id();

        // 1. Total Protocols
        $total = Protocol::where('user_id', $userId)->count();

        // 2. Phase 1: Submission / Draft
        $submission = Protocol::where('user_id', $userId)->where('status_id', 7)->count();

        // 3. Phase 2: Review Stages
        $onReview = Protocol::where('user_id', $userId)->where('status_id', 4)->count();
        $fullBoard = Protocol::where('user_id', $userId)->where('status_id', 2)->count();
        $expedited = Protocol::where('user_id', $userId)->where('status_id', 3)->count();

        // 4. Phase 3: Final / Certificate
        $exempted = Protocol::where('user_id', $userId)->whereIn('status_id', [1, 5])->count();

        return [
            Stat::make('Total Applications', $total)
                ->description('Overall submitted protocols')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('New Submission', $submission)
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('On Review', $onReview)
                ->description('Currently being reviewed')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('warning'),

            Stat::make('Full Board / Expedited', $fullBoard + $expedited)
                ->description('Advanced review needed')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('danger'),

            Stat::make('Exempted', $exempted)
                ->description('Protocol passed review')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Ready to Print', $exempted)
                ->description('Certificates available')
                ->descriptionIcon('heroicon-m-printer')
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href='/user/protocols'",
                ]),
        ];
    }
}
