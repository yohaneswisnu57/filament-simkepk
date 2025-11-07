<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

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
                                ->label('Upload Pernyataan'),
                                
                            TextEntry::make('buktipembayaran')
                                ->label('Bukti Pembayaran'),
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
                    
                
                
                
            ]);
    }
}
