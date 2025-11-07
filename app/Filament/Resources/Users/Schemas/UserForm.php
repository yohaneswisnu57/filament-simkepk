<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->password()
                        // 1. Hash password hanya jika field diisi
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        // 2. Hanya simpan (hydrate) ke database jika field diisi
                        ->dehydrated(fn ($state) => filled($state))
                        // 3. Wajibkan hanya di halaman 'create'
                        ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)
                    ->maxLength(255),
                    Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    // Select::make('reviewer_kelompok_id')
                    //     ->label('Kelompok Reviewer')
                    //     ->relationship('reviewerKelompok', 'name')
                ])
            ]);
    }
}
