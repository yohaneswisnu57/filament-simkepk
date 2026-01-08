<?php

namespace App\Filament\Resources\Protocols\RelationManagers;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;


class DocumentRelationManager extends RelationManager
{
    // protected static string $relationship = 'document';

    // protected static ?string $relatedResource = DocumentResource::class;

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             TextInput::make('namadokumen')
    //                 ->label('Nama Dokumen')
    //                 ->required(),
    //             Select::make('jenisdokumen')
    //                 ->options([
    //                     'docx' => 'Docx',
    //                     'pdf' => 'PDF',
    //                 ])
    //                 ->label('Jenis Dokumen')
    //                 ->required(),
    //             TextInput::make('user.name')
    //                 ->label('User ID')
    //                 ->required()
    //                 ->readOnly(true)
    //                 ->hidden()
    //                 ->default(Auth::id()),
    //             Select::make('protocol_id')
    //                 ->required()
    //                 ->relationship('protocol', 'perihal_pengajuan'),
    //             FileUpload::make('path')
    //                 ->label('Upload Dokumen')
    //                 ->disk('public')
    //                 ->directory('dokumen_pendukung')
    //                 ->preserveFilenames()
    //                 ->required(),
    //                 // ->maxSize(10240) // Maksimum ukuran file 10MB

    //         ]);
    // }

    // public function table(Table $table): Table
    // {
    //     return $table

    //         ->columns([
    //             TextColumn::make('namadokumen')
    //                 ->searchable(),
    //             TextColumn::make('jenisdokumen')
    //                 ->searchable(),
    //             TextColumn::make('user.name')
    //                 ->numeric()
    //                 ->sortable(),
    //             TextColumn::make('protocol.perihal_pengajuan')
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
    //         ->headerActions([
    //             CreateAction::make(),
    //             // EditAction::make()

    //         ]);
    // }

    /* relasi manager untuk document */

    protected static string $relationship = 'document';
    protected static ?string $title = 'Documents';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('namadokumen')
                    ->label('Name Document')
                    ->required(),

                Select::make('jenisdokumen')
                    ->label('Type Document')
                    ->options([
                        'docx' => 'Docx',
                        'pdf' => 'PDF',
                    ])
                    ->required(),

                FileUpload::make('path')
                    ->label('Upload Document')
                    ->disk('public')
                    ->directory('dokumen_pendukung')
                    ->preserveFilenames()
                    ->required()
                    ->maxSize(10240), // maksimal 10MB

                // Otomatis isi user_id dengan user yang sedang login
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),

                // Ambil id protocol dari parent Relation Manager
                Hidden::make('protocol_id')
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->id),
        ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): string{
        return $ownerRecord->document()->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('namadokumen')
                    ->label('Name Document')
                    ->searchable(),

                TextColumn::make('jenisdokumen')
                    ->label('Type Document')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Upload By')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Document')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Pastikan user_id dan protocol_id tetap terisi
                        $data['user_id'] = $data['user_id'] ?? Auth::id();
                        return $data;
                    })
                    ->successNotificationTitle('Success add document'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }
}
