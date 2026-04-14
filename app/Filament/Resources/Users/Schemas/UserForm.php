<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
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
                            ->label('Name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active User')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Leave empty if you do not want to change the password.'
                                : ''
                            )
                            // Hash password hanya jika field diisi
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            // Hanya simpan ke database jika field diisi
                            ->dehydrated(fn ($state) => filled($state))
                            // Wajib hanya saat create, opsional saat edit
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        // Select::make('reviewer_kelompok_id')
                        //     ->label('Kelompok Reviewer')
                        //     ->relationship('reviewerKelompok', 'name')
                    ]),
            ]);
    }
}
