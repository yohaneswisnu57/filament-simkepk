<?php

namespace App\Filament\Resources\StatusReviews;

use App\Filament\Resources\StatusReviews\Pages\CreateStatusReview;
use App\Filament\Resources\StatusReviews\Pages\EditStatusReview;
use App\Filament\Resources\StatusReviews\Pages\ListStatusReviews;
use App\Filament\Resources\StatusReviews\Pages\ViewStatusReview;
use App\Filament\Resources\StatusReviews\Schemas\StatusReviewForm;
use App\Filament\Resources\StatusReviews\Schemas\StatusReviewInfolist;
use App\Filament\Resources\StatusReviews\Tables\StatusReviewsTable;
use App\Models\StatusReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StatusReviewResource extends Resource
{
    protected static ?string $model = StatusReview::class;

    protected static ?string $slug = 'status-reviews';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'StatusReview';

    public static function form(Schema $schema): Schema
    {
        return StatusReviewForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StatusReviewInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatusReviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatusReviews::route('/'),
            'create' => CreateStatusReview::route('/create'),
            'view' => ViewStatusReview::route('/{record}'),
            'edit' => EditStatusReview::route('/{record}/edit'),
        ];
    }
}
