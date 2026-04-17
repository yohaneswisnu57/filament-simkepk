<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pertanyaan')
                    ->schema([
                        TextInput::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('answer')
                            ->label('Jawaban')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'link', 'orderedList', 'bulletList', 'h2', 'h3'
                            ]),
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
