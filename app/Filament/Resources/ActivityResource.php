<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages\ListActivities;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Type')
                    ->badge()
                    ->color('info'),

                TextColumn::make('description')
                    ->label('Action')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function (Activity $record) {
                        return $record->subject?->perihal_pengajuan 
                            ?? $record->subject?->name 
                            ?? $record->subject_type;
                    })
                    ->wrap(),

                TextColumn::make('causer.name')
                    ->label('Performed By')
                    ->placeholder('System'),

                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('D, d M Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
