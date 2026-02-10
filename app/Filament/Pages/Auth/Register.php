<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    public function form(Schema $form): Schema
    {
        return parent::form($form)->schema([
            TextInput::make('name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(),
            TextInput::make('institusi')
                ->label('Institution')
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->required()
                ->confirmed(),

            TextInput::make('password_confirmation')
                ->password()
                ->required(),
        ]);
    }

    protected function handleRegistration(array $data): User
    {
        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'institution' => $data['institusi'],
        //     'password' => Hash::make($data['password']),
        // ]);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'institution' => $data['institusi'],
                'password' => Hash::make($data['password']),
            ]);

            // ✅ AUTO ASSIGN ROLE
            $user->assignRole('user');

            return $user;
        });
    }


    protected function shouldLoginAfterRegistration()
    {
        return false;
    }

    protected function getRedirectUrlAfterRegistration()
    {
        return $this->loginAction()->getUrl();
    }
}
