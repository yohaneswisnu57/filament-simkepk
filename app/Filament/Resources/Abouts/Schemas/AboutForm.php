<?php

namespace App\Filament\Resources\Abouts\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class AboutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Tentang Kami')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Bagian')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('content')
                            ->label('Konten Deskripsi')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'link', 'orderedList', 'bulletList', 'h2', 'h3'
                            ]),
                        FileUpload::make('image_path')
                            ->label('Gambar Pendukung')
                            ->image()
                            ->directory('abouts')
                            ->preserveFilenames()
                            ->maxSize(2048),
                        Toggle::make('is_active')
                            ->label('Tampilkan di Halaman Utama')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ])->columns(1),
            ]);
    }
}
