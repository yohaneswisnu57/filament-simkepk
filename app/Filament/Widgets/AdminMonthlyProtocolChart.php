<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class AdminMonthlyProtocolChart extends ChartWidget
{
    protected ?string $heading = 'Protocol Submission Trends (Per Month)';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::user()->hasRole(['admin', 'super_admin', 'sekertaris']);
    }

    protected function getData(): array
    {
        $data = Trend::model(Protocol::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Protocols Submitted',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#10b981', // Emerald Green
                    'borderColor' => '#10b981',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
