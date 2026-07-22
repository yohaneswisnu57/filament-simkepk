<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ReviewerProtocolStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        // Only visible to reviewers
        return Auth::user()?->hasRole(['reviewer', 'panel_reviewer', 'Ketua Reviewer']) ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        // 1. Pending Tasks (Needs Review)
        $pending = Protocol::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('reviewers', function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                        ->where('feedback_status', 'pending');
                })
                    ->orWhere(function ($q) use ($user) {
                        if ($user->reviewer_kelompok_id) {
                            $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                    $q2->where('users.id', $user->id);
                                })
                                ->whereDoesntHave('reviews', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })->count();

        // 2. Completed Tasks (Finished Review)
        $completed = Protocol::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('reviewers', function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                        ->where('feedback_status', 'submitted');
                })
                    ->orWhere(function ($q) use ($user) {
                        if ($user->reviewer_kelompok_id) {
                            $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                    $q2->where('users.id', $user->id);
                                })
                                ->whereHas('reviews', function ($q2) use ($user) {
                                    $q2->where('user_id', $user->id);
                                });
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })->count();

        // 3. Total Assignments
        $total = $pending + $completed;

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
