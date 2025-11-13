<?php

namespace App\Filament\Resources\Protocols\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    protected static ?string $title = 'Riwayat Review'; // Judul custom

    public function form(Schema $schema): Schema
    {
        // Form ini digunakan untuk "Create" dan "Edit"
        return $schema
            ->components([
                Textarea::make('comment')
                    ->required()
                    ->columnSpanFull(),
                // Kita tidak perlu 'user_id' karena akan diisi otomatis
            ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): string{
        return $ownerRecord->reviews()->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->columns([
                TextColumn::make('user.name') // Tampilkan nama Reviewer
                    ->label('Reviewer')
                    ->searchable(),
                TextColumn::make('comment')
                    ->wrap() // Agar teks panjang bisa turun
                    ->markdown(), // Bisa pakai markdown
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tombol "New Review" di atas tabel RM
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Tambahkan 'user_id' secara otomatis saat membuat
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Opsi: Sembunyikan jika user bukan admin/reviewer
    public static function canViewForRecord(Model $ownerRecord, string $pageName): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'super_admin', 'reviewer', 'sekertaris']);
    }
    // protected static string $relationship = 'reviews';

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             TextInput::make('user_id')
    //                 ->required()
    //                 ->numeric(),
    //             Textarea::make('comment')
    //                 ->required()
    //                 ->columnSpanFull(),
    //         ]);
    // }

    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->recordTitleAttribute('comment')
    //         ->columns([
    //             TextColumn::make('user_id')
    //                 ->numeric()
    //                 ->sortable(),
    //             TextColumn::make('created_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             TextColumn::make('deleted_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->headerActions([
    //             CreateAction::make(),
    //             AssociateAction::make(),
    //         ])
    //         ->recordActions([
    //             EditAction::make(),
    //             DissociateAction::make(),
    //             DeleteAction::make(),
    //         ])
    //         ->toolbarActions([
    //             BulkActionGroup::make([
    //                 DissociateBulkAction::make(),
    //                 DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }


}
