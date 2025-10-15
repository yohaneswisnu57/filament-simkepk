<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProtocolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('perihal_pengajuan'),
                TextEntry::make('jenis_protocol'),
                TextEntry::make('tanggal_pengajuan')
                    ->dateTime(),
                TextEntry::make('StatusReview.status_name')
                    ->numeric(),
                TextEntry::make('uploadpernyataan'),
                TextEntry::make('buktipembayaran'),
                TextEntry::make('user.name')
                    ->numeric(),
                TextEntry::make('tgl_mulai_review')
                    ->date(),
                TextEntry::make('tgl_selesai_review')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Protocol $record): bool => $record->trashed()),
            ]);
    }
}
