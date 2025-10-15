<?php

namespace App\Filament\Resources\StatusReviews\Pages;

use App\Filament\Resources\StatusReviews\StatusReviewResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStatusReview extends ViewRecord
{
    protected static string $resource = StatusReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
