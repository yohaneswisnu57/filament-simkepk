<?php

namespace App\Filament\Resources\Protocols;

use App\Filament\Resources\Protocols\Pages\CreateProtocol;
use App\Filament\Resources\Protocols\Pages\EditProtocol;
use App\Filament\Resources\Protocols\Pages\ListProtocols;
use App\Filament\Resources\Protocols\Pages\ViewProtocol;
use App\Filament\Resources\Protocols\Schemas\ProtocolForm;
use App\Filament\Resources\Protocols\Schemas\ProtocolInfolist;
use App\Filament\Resources\Protocols\Tables\ProtocolsTable;
use App\Models\Protocol;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProtocolResource extends Resource
{
    protected static ?string $model = Protocol::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $recordTitleAttribute = 'Protocol';

    public static function form(Schema $schema): Schema
    {
        return ProtocolForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProtocolInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProtocolsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentRelationManager::class,
        ];
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
    

    public static function getPages(): array
    {
        return [
            'index' => ListProtocols::route('/'),
            'create' => CreateProtocol::route('/create'),
            'view' => ViewProtocol::route('/{record}'),
            'edit' => EditProtocol::route('/{record}/edit'),
        ];
    }
}
