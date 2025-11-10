<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProtocolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Protocol')
                    // ->label('Informasi Protocol')
                    ->schema([
                        TextEntry::make('perihal_pengajuan'),
                        TextEntry::make('jenis_protocol'),
                        TextEntry::make('tanggal_pengajuan')
                            ->dateTime(),
                        TextEntry::make('StatusReview.status_name')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'FULL BOARD' => 'success',
                                'EXEMPTED' => 'warning',
                                'EXPEDITED' => 'success',
                                default => 'gray',
                            })

                            ->numeric(),
                    ]),
                Section::make('Document')
                    // ->label('Informasi Protocol')
                    ->schema([
                            TextEntry::make('uploadpernyataan')
                                ->label('Upload Pernyataan')
                                ->beforeContent(Icon::make(Heroicon::Folder))
                                ->formatStateUsing(fn (?string $state): ?string => basename($state))
                                ->action(Action::make('downloadFile')
                                        ->label('Download File')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->color('info')
                                        // Logika download
                                        ->action(function (Protocol $record) {
                                            // Ganti 'public' jika disk Anda berbeda
                                            return Storage::disk('public')->download($record->uploadpernyataan);
                                        })
                                        // Sembunyikan tombol jika tidak ada file
                                        ->visible(fn (Protocol $record): bool => !empty($record->uploadpernyataan))
                                ),
                            TextEntry::make('buktipembayaran')
                                ->label('Bukti Pembayaran')
                                ->beforeContent(Icon::make(Heroicon::Folder))
                                ->formatStateUsing(fn (?string $state): ?string => basename($state))
                                ->action(Action::make('downloadFile')
                                        ->label('Download File')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->color('info')
                                        // Logika download
                                        ->action(function (Protocol $record) {
                                            // Ganti 'public' jika disk Anda berbeda
                                            return Storage::disk('public')->download($record->buktipembayaran);
                                        })
                                        // Sembunyikan tombol jika tidak ada file
                                        ->visible(fn (Protocol $record): bool => !empty($record->buktipembayaran))
                                ),
                            TextEntry::make('user.name')->label('Created By')
                                ->numeric(),
                    ]),
                Section::make('Review Timeline')
                    // ->label('Informasi Protocol')
                    ->schema([
                            TextEntry::make('tgl_mulai_review')
                                ->label('Tanggal Mulai Review')
                                ->placeholder('-')
                                ->date(),
                            TextEntry::make('tgl_selesai_review')
                                ->label('Tanggal Selesai Review')
                                ->placeholder('-')
                                ->date(),
                    ]),

                Section::make('Timestamps')
                    // ->label('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ]),
            ]);
    }
}
