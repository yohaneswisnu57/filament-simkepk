<?php

namespace App\Filament\Resources\StatusReviews\Schemas;

use App\Models\StatusReview;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StatusReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('status_name'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (StatusReview $record): bool => $record->trashed()),
            ]);
    }
}
