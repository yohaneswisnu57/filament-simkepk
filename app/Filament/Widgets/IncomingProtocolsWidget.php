<?php

namespace App\Filament\Widgets;

use App\Models\Protocol;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IncomingProtocolsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Protokol Masuk';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Protocol::query()->latest('created_at')
            )
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Judul Pengajuan Penelitian')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Pengusul')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_protocol')
                    ->label('Jenis Penelitian')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Manusia' => 'info',
                        'Hewan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Diajukan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('reviewers.name')
                    ->label('Penelaah yang Ditunjuk')
                    ->badge()
                    ->default('Penelaah belum dipilih')
                    ->color(fn ($state, Protocol $record): string => $record->reviewers->isEmpty() ? 'warning' : 'success')
                    ->url(fn (Protocol $record): ?string => $record->reviewers->isEmpty()
                        ? \App\Filament\Resources\Protocols\ProtocolResource::getUrl('edit', ['record' => $record])
                        : null)
                    ->searchable(),

                TextColumn::make('statusReview.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                        'FULL BOARD' => 'warning',
                        'EXEMPTED' => 'primary',
                        'CERTIFICATE' => 'primary',
                        'EXPEDITED' => 'primary',
                        'FAST REVIEW' => 'primary',
                        'SUBMISSION' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ]);
    }
}
