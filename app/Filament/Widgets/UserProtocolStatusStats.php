<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserProtocolStatusStats extends StatsOverviewWidget
{
    // Atur urutan tampilan (opsional)
    protected static ?int $sort = 1;

    // 👇 LOGIC PENTING: Hanya tampil jika user adalah Peneliti/User biasa
    public static function canView(): bool
    {
        // Sesuaikan dengan nama role Anda ('user' atau 'peneliti')
        return Auth::user()->hasRole(['user', 'peneliti']);
    }

    protected function getStats(): array
    {
        $userId = Auth::id();

        // Asumsi: status_id 0=Draft, 1=Diajukan, 2=Revisi, 3=Approved
        // Sesuaikan query ini dengan logic Enum atau ID status di database Anda

        $draft = Protocol::where('user_id', $userId)->where('status_id', 0)->count();
        $onProses = Protocol::where('user_id', $userId)->where('status_id', 1)->count();
        $exempted = Protocol::where('user_id', $userId)->where('status_id', 2)->count();
        $expedited = Protocol::where('user_id', $userId)->where('status_id', 3)->count();
        $fullboard = Protocol::where('user_id', $userId)->where('status_id', 3)->count();

        return [
            Stat::make('New Submission', $draft)
                ->description('New Protocol')
                ->color('blue'),

            Stat::make('On Proses', $onProses)
                ->description('Menunggu review')
                ->color('warning'),

            Stat::make('EXEMPTED', $exempted)
                ->description('PASSED PRINT CERTIFICATE')
                ->color('success'),

            Stat::make('EXPEDITED', $expedited)
                ->description('USER REVISION')
                ->color('success'),

            Stat::make('FULLBOARD', $fullboard)
                ->description('Sertifikat terbit')
                ->color('success'),
        ];
    }
}
