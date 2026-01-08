<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // protected static ?string $recordTitleAttribute = 'Document';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('namadokumen')
                    ->label('Name Document')
                    ->required(),
                Select::make('jenisdokumen')
                    ->options([
                        'docx' => 'Docx',
                        'pdf' => 'PDF',
                    ])
                    ->label('Document Type')
                    ->required(),
                Select::make('protocol_id')
                    // ->searchable()
                    ->required()
                    ->relationship('protocol', 'perihal_pengajuan')
                    ->label('Protocol')
                    ->when(fn (Select $component) => !Auth::user()->hasRole('super_admin') && !Auth::user()->hasRole('admin'), function (Select $component) {
                        $userId = Auth::id();
                        $component->options(fn () => Document::whereHas('protocol', function (Builder $query) use ($userId) {
                            $query->where('user_id', $userId);
                        })->with('protocol')->get()->pluck('protocol.perihal_pengajuan', 'protocol.id')->unique());
                    }),
                // FileUpload::make('path')
                //     ->label('Upload Document')
                //     ->disk('public')
                //     ->directory('dokumen_pendukung')
                //     ->preserveFilenames()
                //     ->required(),
                //     // ->maxSize(10240) // Maksimum ukuran file 10MB
                FileUpload::make('path')
                            ->label('Upload Document')
                            ->required()
                            ->preserveFilenames()
                            ->disk('public')
                            ->directory('dokumen_pendukung')
                            ->acceptedFileTypes([
                                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ]) // Opsional: Batasi hanya PDF/Docx
                            ->maxSize(3072) // <--- Batasan 3MB (3072 KB)
                            ->validationMessages([
                                'max' => 'Ukuran file terlalu besar. Maksimal hanya 3MB.',
                            ]),

            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('namadokumen')
                    ->label('Document Name'),
                TextEntry::make('jenisdokumen')
                    ->label('Document Type'),
                TextEntry::make('user_id')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('protocol.perihal_pengajuan')
                    ->label('Concerning'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Document $record): bool => $record->trashed()),
                TextEntry::make('path')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Document')
            ->columns([
                TextColumn::make('namadokumen')
                    ->label('Document Name')
                    ->searchable(),
                TextColumn::make('jenisdokumen')
                    ->label('Document Type')
                    ->searchable(),
                // TextColumn::make('user.name')
                //     ->label('User')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('protocol.perihal_pengajuan')
                    ->label('Concerning')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        return $data;
    }

    public static function getEloquentQuery(): Builder{
        $user = auth()->user();

        $query = parent::getEloquentQuery();
        // dd($query);
        // Ganti 'Admin' dengan nama peran admin Anda jika berbeda
        // Logika ini: "Jika pengguna TIDAK memiliki peran Admin..."
        if (!$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            // "...maka filter data hanya untuk user_id miliknya."
            $query->where('user_id', $user->id);
        }

        // Admin akan melewati 'if' dan mendapatkan semua data
        return $query;
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
