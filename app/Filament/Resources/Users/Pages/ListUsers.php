<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Actions\ImportReviewerAction;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportReviewerAction::make(),
            Action::make('downloadTemplate')
                ->label('Template CSV')
                ->icon(Heroicon::ArrowDownTray)
                ->color('gray')
                ->url(route('downloads.import-reviewer-template'))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
