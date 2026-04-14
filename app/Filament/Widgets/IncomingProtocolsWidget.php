<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class IncomingProtocolsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    protected static ?string $heading = 'All Incoming Protocols';

    public static function canView(): bool
    {
        return Auth::user()->hasRole(['admin', 'super_admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Protocol::query()->latest('created_at')
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

                TextColumn::make('reviewers.name')
                    ->label('Assigned Reviewers')
                    ->badge()
                    ->default('None assigned')
                    ->color(fn ($state, Protocol $record): string => $record->reviewers->isEmpty() ? 'warning' : 'success')
                    ->searchable(),

                TextColumn::make('statusReview.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                        'FULL BOARD' => 'danger',
                        'EXEMPTED' => 'success',
                        'EXPEDITED' => 'info',
                        'FAST REVIEW' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ]);
    }
}
