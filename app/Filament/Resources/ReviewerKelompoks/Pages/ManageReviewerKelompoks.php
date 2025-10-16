<?php

namespace App\Filament\Resources\ReviewerKelompoks\Pages;

use App\Filament\Resources\ReviewerKelompoks\ReviewerKelompokResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageReviewerKelompoks extends ManageRecords
{
    protected static string $resource = ReviewerKelompokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
