<?php

namespace App\Filament\Resources\Protocols\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProtocolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Perihal')
                    ->searchable(),
                // TextColumn::make('User.name')
                //     ->label('User')
                //     ->numeric(),
                TextColumn::make('jenis_protocol')
                    ->label('Jenis Protocol')
                    ->searchable(),
                TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y')
                    ->sortable(),
                // ImageColumn::make('uploadpernyataan')
                //     ->label('Pernyataan')
                //     ->disk('public'),
                // ImageColumn::make('buktipembayaran')
                //     ->label('Bukti Pembayaran')
                //     ->disk('public'),
                TextColumn::make('tgl_mulai_review')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tgl_selesai_review')
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
