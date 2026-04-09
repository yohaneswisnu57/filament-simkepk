<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AdminUnassignedProtocolsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Protocols Needing Assignment';

    public static function canView(): bool
    {
        return Auth::user()->hasRole(['admin', 'super_admin', 'sekertaris']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Protocol::query()
                    ->whereNull('reviewer_kelompok_id')
                    ->where('status_id', 7) // Submission
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Research Title')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Researcher')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_protocol')
                    ->label('Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Manusia' => 'info',
                        'Hewan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Submission Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('statusReview.status_name')
                    ->label('Current Status')
                    ->badge()
                    ->color('danger'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Assign Reviewers')
                    ->icon('heroicon-m-user-plus')
                    ->url(fn (Protocol $record): string => \App\Filament\Resources\Protocols\ProtocolResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
