<?php

namespace App\Filament\Resources\StatusReviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StatusReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status_name')
                    ->required(),
            ]);
    }
}
