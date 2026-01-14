<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AdminMonthlyProtocolChart extends ChartWidget
{
    protected ?string $heading = 'Admin Monthly Protocol Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
