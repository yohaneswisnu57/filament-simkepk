<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ReviewerProtocolStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        // Only visible to reviewers
        return Auth::user()->hasRole('reviewer');
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // 1. Pending Tasks (Needs Review)
        $pending = $user->assignedProtocols()->wherePivot('feedback_status', 'pending')->count();

        // 2. Completed Tasks (Finished Review)
        $completed = $user->assignedProtocols()->wherePivot('feedback_status', 'submitted')->count();

        // 3. Total Assignments
        $total = $user->assignedProtocols()->count();

        return [
            Stat::make('Pending Tasks', $pending)
                ->description('Needs your review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Completed Tasks', $completed)
                ->description('Reviews submitted')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Assignments', $total)
                ->description('Total workload assigned')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('gray'),
        ];
    }
}
