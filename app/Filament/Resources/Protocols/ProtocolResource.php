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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProtocolResource extends Resource
{
    protected static ?string $model = Protocol::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    // protected static ?string $recordTitleAttribute = 'Protocol';

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
            // RelationManagers\ReviewsRelationManager::class,
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();
    //     $user = auth()->user();

    //     // 1. GABUNGKAN pengecekan 'super_admin' dan 'admin' di sini
    //     // Jika user adalah 'super_admin' ATAU 'admin', berikan semua data
    //     if ($user->hasRole(['super_admin', 'admin', 'sekertaris','reviewer'])) {
    //         return $query; // Selesai, tidak perlu filter lagi
    //     }

    //     // 2. Jika BUKAN super_admin atau admin, terapkan filter ketat

    //     // Dapatkan ID kelompok reviewer dari user yang login
    //     $userReviewerKelompokId = $user->reviewer_kelompok_id;
    //     // dd($userReviewerKelompokId);

    //     // Filter kueri HANYA untuk user biasa/reviewer
    //     $query->where(function (Builder $q) use ($user, $userReviewerKelompokId) {

    //         // Pengguna selalu bisa melihat protokol yang diajukan olehnya
    //         $q->where('user_id', $user->id);

    //         // JIKA pengguna adalah bagian dari kelompok reviewer...
    //         if ($userReviewerKelompokId) {
    //             // ...dia JUGA bisa melihat protokol yang di-assign ke kelompoknya
    //             $q->orWhere('reviewer_kelompok_id', $userReviewerKelompokId);
    //         }
    //     });

    //     return $query;
    // }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Cek Role Admin/Super Admin/Sekertaris
        // HAPUS 'reviewer' dari sini agar logika filter di bawah bisa berjalan untuk reviewer
        if ($user->hasRole(['super_admin', 'admin', 'sekertaris'])) {
            return $query; // Mereka melihat semua data
        }

        // 2. Logika untuk Reviewer dan User Biasa
        $query->where(function (Builder $q) use ($user) {

            // A. Semua user (termasuk reviewer) BISA melihat protokol milik sendiri
            $q->where('user_id', $user->id);

            // B. JIKA dia adalah Reviewer DAN punya Kelompok ID
            // Maka dia juga bisa melihat protokol yang di-assign ke kelompoknya
            if ($user->hasRole('reviewer') && $user->reviewer_kelompok_id) {
                $q->orWhere('reviewer_kelompok_id', $user->reviewer_kelompok_id);
            }
        });

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

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
