<?php

namespace App\Filament\Resources\StatusReviews\Pages;

use App\Filament\Resources\StatusReviews\StatusReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStatusReview extends EditRecord
{
    protected static string $resource = StatusReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
