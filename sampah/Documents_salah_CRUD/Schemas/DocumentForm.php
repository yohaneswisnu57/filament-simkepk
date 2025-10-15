<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('namadokumen')
                    ->label('Nama Dokumen')
                    ->required(),
                Select::make('jenisdokumen')
                    ->options([
                        'docx' => 'Docx',
                        'pdf' => 'PDF',
                    ])
                    ->label('Jenis Dokumen')
                    ->required(),
                TextInput::make('user.name')
                    ->label('User ID')
                    ->required()
                    ->readOnly(true)
                    ->hidden()
                    ->default(Auth::id()),
                Select::make('protocol_id')
                    ->required()
                    ->relationship('protocol', 'perihal_pengajuan'),
                FileUpload::make('path')
                    ->label('Upload Dokumen')
                    ->disk('public')
                    ->directory('dokumen_pendukung')
                    ->preserveFilenames()
                    ->required(),
                    // ->maxSize(10240) // Maksimum ukuran file 10MB

            ]);
    }
}
