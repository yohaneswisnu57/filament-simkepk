<?php

namespace App\Filament\Resources\Protocols\Widgets;

use App\Models\Protocol;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()->hasRole(['admin', 'super_admin', 'sekertaris']);
    }

    protected function getStats(): array
    {
        // 1. Pending Assignment (New Submission + No Reviewer Group)
        $pendingAssignment = Protocol::where('status_id', 7)
            ->whereNull('reviewer_kelompok_id')
            ->count();

        // 2. Active Reviews (Protocols in review stages)
        // IDs: 2 (Full Board), 3 (Expedited), 4 (On Review), 6 (Fast Review)
        $activeReviews = Protocol::whereIn('status_id', [2, 3, 4, 6])->count();

        // 3. Ready for Certificate (Exempted by Reviewers, but Certificate not yet issued by Admin)
        // Status ID 1 (Exempted) and 5 (Certificate) are final; others are pending.
        $readyForCertificate = Protocol::where('fast_review_decision', 'Exempted')
            ->whereNotIn('status_id', [1, 5])
            ->count();

        // 4. Total Exempted (Historical success)
        // Includes both 'Exempted' (1) and 'Certificate' (5) statuses.
        $totalExempted = Protocol::whereIn('status_id', [1, 5])->count();

        return [
            Stat::make('Pending Assignment', $pendingAssignment)
                ->description('New protocols needing reviewers')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('danger'),

            Stat::make('Active Reviews', $activeReviews)
                ->description('Protocols currently being processed')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Ready for Certificate', $readyForCertificate)
                ->description('Awaiting Admin issuance')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),

            Stat::make('Total Exempted', $totalExempted)
                ->description('Overall successful studies')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
