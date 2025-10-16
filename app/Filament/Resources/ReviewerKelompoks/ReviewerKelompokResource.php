<?php

namespace App\Filament\Resources\ReviewerKelompoks;

use App\Filament\Resources\ReviewerKelompoks\Pages\ManageReviewerKelompoks;
use App\Models\ReviewerKelompok;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewerKelompokResource extends Resource
{
    protected static ?string $model = ReviewerKelompok::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ReviewerKelompok';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kelompok_reviewer')
                    ->label('Kelompok Reviewer')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ReviewerKelompok')
            ->columns([
                TextColumn::make('kelompok_reviewer')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReviewerKelompoks::route('/'),
        ];
    }
}
