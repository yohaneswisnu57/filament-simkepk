<?php

namespace App\Filament\Resources\Protocols\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ProtocolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Research Title')
                    ->searchable()
                    ->wrap()
                    ->limit(60),

                TextColumn::make('user.name')
                    ->label('Researcher')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

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

                TextColumn::make('fast_review_decision')
                    ->label('Decision')
                    ->badge()
                    ->placeholder('-')
                    ->color(fn (?string $state): string => match ($state) {
                        'Exempted' => 'success',
                        'Full Board' => 'danger',
                        'Pending' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('tgl_mulai_review')
                    ->label('Start Review')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgl_selesai_review')
                    ->label('End Review')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('tanggal_pengajuan')
                    ->label('Filter by Submission Date')
                    ->form([
                        DatePicker::make('tanggal_pengajuan')
                            ->label('Submission Date')
                            ->native(false),
                    ])
                    ->query(fn ($query, $data) => $query->when(
                        $data['tanggal_pengajuan'],
                        fn ($q, $date) => $q->whereDate('tanggal_pengajuan', $date)
                    )),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
