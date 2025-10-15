<?php

namespace App\Filament\Resources\Document\Schemas;

use App\Models\Document;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('namadokumen'),

                TextEntry::make('jenisdokumen')
                    ,
                TextEntry::make('user.name')
                    ,
                TextEntry::make('protocol.perihal_pengajuan')
                    ,
                TextEntry::make('path')
                    ,
                TextEntry::make('created_at')
                    ,
                TextEntry::make('updated_at')
                    ,
            ]);
    }
}
