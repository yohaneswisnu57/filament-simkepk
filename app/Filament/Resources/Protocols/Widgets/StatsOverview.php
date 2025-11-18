<?php

namespace App\Filament\Resources\Protocols\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use App\Models\Protocol;

class StatsOverview extends StatsOverviewWidget
{
    // Properti kolom span dan urutan (opsional)
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // --- 1. Perhitungan Data Protokol ---

        // Periode Saat Ini (Protokol Bulan Ini)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $protocolCountCurrent = Protocol::where('created_at', '>=', $currentMonthStart)->count();

        // Periode Sebelumnya (Protokol Bulan Lalu)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->startOfMonth();

        $protocolCountPrevious = Protocol::where('created_at', '>=', $lastMonthStart)
                                       ->where('created_at', '<', $lastMonthEnd)
                                       ->count();

        // --- 2. Perhitungan Delta (Perubahan Persentase) ---

        if ($protocolCountPrevious > 0) {
            $delta = $protocolCountCurrent - $protocolCountPrevious;
            $percentageChange = round(($delta / $protocolCountPrevious) * 100, 2);

            // Konfigurasi tampilan
            $descriptionText = abs($percentageChange) . '% ' . ($delta >= 0 ? 'increase' : 'decrease') . ' from last month';
            $color = $delta >= 0 ? 'success' : 'danger'; // Hijau untuk naik, Merah untuk turun
            $icon = $delta >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        } else {
            // Default jika bulan lalu tidak ada data
            $descriptionText = 'New data available (No comparison)';
            $color = 'info';
            $icon = 'heroicon-o-information-circle';
        }

        // --- 3. Siapkan Data Tren (Chart) 7 Hari Terakhir ---

        $protocolTrendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $count = Protocol::whereDate('created_at', $date)->count();
            $protocolTrendData[] = $count;
        }

        // --- 4. Stat Card Protokol ---

        return [
            Stat::make('Total Protocols', $protocolCountCurrent)
                ->chart($protocolTrendData)
                ->description($descriptionText)
                ->descriptionIcon($icon)
                ->color($color),

            // Anda bisa menambahkan Stat lain di sini jika diperlukan
            // Stat::make('Total Users', \App\Models\User::count()),
        ];
    }
}
