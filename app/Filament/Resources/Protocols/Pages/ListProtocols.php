<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Contracts\HasLabel;
use App\Models\Protocol;

class ListProtocols extends ListRecords
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     $user = auth()->user();
    //     if (!$user->hasRole('super_admin') && !$user->hasRole('admin')) {
    //         // Jika bukan admin, batasi hanya ke data milik user tersebut
    //         // Ganti 'status_id' jika nama kolom Anda berbeda
    //         $statusColumn = 'status_id';

    //         // GANTI ID (angka) di bawah ini sesuai database Anda
    //         $prosesId = 0; // ❓ Ganti 4 dengan ID untuk 'Proses Pengajuan'
    //         $exemptedId = 1;
    //         $expeditedId = 3;
    //         $fullboardId = 2;

    //         return [
    //             'all' => Tab::make('Semua')
    //                 ->badge(Protocol::query()->where('user_id', $user->id)->count()) // Jumlah total
    //                 ->query(fn (Builder $query) => $query->where('user_id', $user->id)),

    //             'proses' => Tab::make('Proses Pengajuan')
    //                 ->badge(Protocol::query()->where($statusColumn, $prosesId)->where('user_id', $user->id)->count())
    //                 ->query(fn (Builder $query) => $query->where($statusColumn, $prosesId)->where('user_id', $user->id)),

    //             'exempted' => Tab::make('Exempted')
    //                 ->badge(Protocol::query()->where($statusColumn, $exemptedId)->where('user_id', $user->id)->count())
    //                 ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)->where('user_id', $user->id)),

    //             'expedited' => Tab::make('Expedited')
    //                 ->badge(Protocol::query()->where($statusColumn, $expeditedId)->where('user_id', $user->id)->count())
    //                 ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)->where('user_id', $user->id)),

    //             'fullboard' => Tab::make('Full Board')
    //                 ->badge(Protocol::query()->where($statusColumn, $fullboardId)->where('user_id', $user->id)->count())
    //                 ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)->where('user_id', $user->id)),
    //         ];
    //     }

    //     return [
    //         'all' => Tab::make('Semua')
    //             ->badge(Protocol::count())
    //             ->query(fn (Builder $query) => $query),

    //         'proses' => Tab::make('Proses Pengajuan')
    //             ->badge(Protocol::where('status_id', 0)->count()) // Ganti 4 dengan ID untuk 'Proses Pengajuan'
    //             ->query(fn (Builder $query) => $query->where('status_id', 0)),

    //         'exempted' => Tab::make('Exempted')
    //             ->badge(Protocol::where('status_id', 1)->count())
    //             ->query(fn (Builder $query) => $query->where('status_id', 1)),

    //         'expedited' => Tab::make('Expedited')
    //             ->badge(Protocol::where('status_id', 3)->count())
    //             ->query(fn (Builder $query) => $query->where('status_id', 3)),

    //         'fullboard' => Tab::make('Full Board')
    //             ->badge(Protocol::where('status_id', 2)->count())
    //             ->query(fn (Builder $query) => $query->where('status_id', 2)),
    //     ];

    // }


    public function getTabs(): array
    {
        $user = auth()->user();

        // 1. Tentukan apakah user adalah Admin (untuk efisiensi)
        $isAdmin = $user->hasRole(['super_admin', 'admin']);

        // 2. Tentukan ID Status Anda
        $statusColumn = 'status_id';
        $prosesId = '';
        $exemptedId = 1;
        $expeditedId = 3;
        $fullboardId = 2;


        // 3. Buat "Closure" (fungsi) untuk Scoping Non-Admin
        // Ini adalah LOGIKA YANG SAMA PERSIS seperti di getEloquentQuery
        $userScope = function (Builder $query) use ($user) {
            // Dapatkan ID kelompok reviewer dari user yang login
            $userReviewerKelompokId = $user->reviewer_kelompok_id;

            $query->where(function (Builder $q) use ($user, $userReviewerKelompokId) {
                // Pengguna bisa melihat protokol yang diajukan olehnya
                $q->where('user_id', $user->id);

                // JIKA pengguna adalah bagian dari kelompok reviewer...
                if ($userReviewerKelompokId) {
                    // ...dia JUGA bisa melihat protokol yang di-assign ke kelompoknya
                    $q->orWhere('reviewer_kelompok_id', $userReviewerKelompokId);
                }
            });

            return $query; // Kembalikan query untuk di-chain
        };


        // --- SEKARANG, KITA BUAT TABS ---

        // 4. Jika user BUKAN Admin, tampilkan Tab yang sudah di-scope
        if (!$isAdmin) {
            return [
                'all' => Tab::make('Semua')
                    // Badge: Panggil scope non-admin
                    ->badge(Protocol::query()->where($userScope)->count()), // <--- PERBAIKAN
                    // Query: TIDAK PERLU, karena 'all' = base query dari getEloquentQuery

                'proses' => Tab::make('Proses Pengajuan')
                    // Badge: Panggil scope non-admin + filter status
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $prosesId)->count()) // <--- PERBAIKAN
                    // Query: HANYA filter status (base-nya sudah di-scope oleh getEloquentQuery)
                    ->query(fn (Builder $query) => $query->where($statusColumn, $prosesId)),

                'exempted' => Tab::make('Exempted')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $exemptedId)->count()) // <--- PERBAIKAN
                    ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)),

                'expedited' => Tab::make('Expedited')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $expeditedId)->count()) // <--- PERBAIKAN
                    ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)),

                'fullboard' => Tab::make('Full Board')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $fullboardId)->count()) // <--- PERBAIKAN
                    ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)),
            ];
        }

        // 5. (Default) Jika user ADALAH Admin, tampilkan semua data
        return [
            'all' => Tab::make('Semua')
                ->badge(Protocol::count()),

            'proses' => Tab::make('Proses Pengajuan')
                ->badge(Protocol::where($statusColumn, $prosesId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $prosesId)),

            'exempted' => Tab::make('Exempted')
                ->badge(Protocol::where($statusColumn, $exemptedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)),

            'expedited' => Tab::make('Expedited')
                ->badge(Protocol::where($statusColumn, $expeditedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)),

            'fullboard' => Tab::make('Full Board')
                ->badge(Protocol::where($statusColumn, $fullboardId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)),
        ];
    }

}
