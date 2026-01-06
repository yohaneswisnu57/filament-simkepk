<?php

namespace App\Filament\Resources\Protocols\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
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
                    ->label('Concerning')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User'),
                TextColumn::make('jenis_protocol')
                    ->label('Type Protocol')
                    ->searchable(),
                TextColumn::make('tanggal_pengajuan')
                    ->label('Submission Date')
                    ->dateTime('d M Y')
                    ->sortable(),
                // ImageColumn::make('uploadpernyataan')
                //     ->label('Pernyataan')
                //     ->disk('public'),
                // ImageColumn::make('buktipembayaran')
                //     ->label('Bukti Pembayaran')
                //     ->disk('public'),
                TextColumn::make('tgl_mulai_review')
                    ->label('Review Start Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tgl_selesai_review')
                    ->label('Review End Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('statusReview.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'FULL BOARD' => 'success',
                        'EXEMPTED' => 'warning',
                        'EXPEDITED' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                Filter::make('created_at')
                ->schema([
                    DatePicker::make('tanggal_pengajuan')
                        ->label('Submission Date'),
                ])
                ->query(function ($query, $data) {
                    return $query->when(
                        $data['tanggal_pengajuan'],
                        fn ($query, $date) => $query->where('tanggal_pengajuan', $date),
                    );
                })
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
