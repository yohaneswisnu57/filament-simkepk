<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;

use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('roles')
                    ->label('Roles')
                    ->getStateUsing(fn ($record) => $record->roles->pluck('name')->join(', '))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('roles', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('reviewerKelompok.nama_kelompok')
                    ->label('Reviewer Group')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //

            ])
            ->recordActions([
                Action::make('impersonate')
                    ->label('Login As')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->requiresConfirmation()
                    ->modalHeading('Login As User')
                    ->form(function ($record) {
                        $options = [];
                        
                        if ($record->hasRole(['super_admin', 'admin'])) {
                            $options['/admin'] = 'Admin Panel (Super Admin / Admin)';
                        }
                        
                        if ($record->hasRole(['panel_reviewer', 'reviewer', 'Ketua', 'sekertaris'])) {
                            $options['/reviewer'] = 'Reviewer Panel (Reviewer / Ketua / Sekertaris)';
                        }
                        
                        if ($record->hasRole(['user', 'panel_user'])) {
                            $options['/user'] = 'User Panel (Peneliti)';
                        }
                        
                        if (empty($options)) {
                            $options[''] = 'User tidak memiliki akses ke panel manapun';
                        }
                        
                        return [
                            Select::make('panel_url')
                                ->label('Pilih Akses Panel')
                                ->options($options)
                                ->default(array_key_first($options))
                                ->required()
                                ->helperText("User ini memiliki beberapa role. Pilih panel mana yang ingin Anda akses sebagai user ini."),
                        ];
                    })
                    ->action(function ($record, array $data, Action $action) {
                        if (empty($data['panel_url'])) {
                            Notification::make()
                                ->title('Akses Ditolak')
                                ->body('User ini tidak memiliki hak akses (role) untuk masuk ke panel manapun. Impersonasi dibatalkan.')
                                ->danger()
                                ->send();
                            
                            $action->cancel();
                        }

                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->log("Impersonated user: {$record->email}");

                        session()->put('impersonated_by', auth()->id());
                        
                        auth()->login($record);
                        session()->put([
                            'password_hash_' . auth()->getDefaultDriver() => $record->getAuthPassword(),
                        ]);
                        session()->regenerate();
                        session()->save();
                        
                        return redirect($data['panel_url']);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
