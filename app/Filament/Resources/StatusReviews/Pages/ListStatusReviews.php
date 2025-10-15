<?php

namespace App\Filament\Resources\StatusReviews\Pages;

use App\Filament\Resources\StatusReviews\StatusReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatusReviews extends ListRecords
{
    protected static string $resource = StatusReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
