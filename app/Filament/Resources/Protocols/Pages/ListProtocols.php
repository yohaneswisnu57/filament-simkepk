<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
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

    public function getTabs(): array
    {
        $user = auth()->user();
        if (!$user->hasRole('super_admin') && !$user->hasRole('admin')) {
            // Jika bukan admin, batasi hanya ke data milik user tersebut
            // Ganti 'status_id' jika nama kolom Anda berbeda
            $statusColumn = 'status_id';

            // GANTI ID (angka) di bawah ini sesuai database Anda
            $prosesId = 0; // ❓ Ganti 4 dengan ID untuk 'Proses Pengajuan'
            $exemptedId = 1;
            $expeditedId = 3;
            $fullboardId = 2;

            return [
                'all' => Tab::make('Semua')
                    ->badge(Protocol::query()->where('user_id', $user->id)->count()) // Jumlah total
                    ->query(fn (Builder $query) => $query->where('user_id', $user->id)),

                'proses' => Tab::make('Proses Pengajuan')
                    ->badge(Protocol::query()->where($statusColumn, $prosesId)->where('user_id', $user->id)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $prosesId)->where('user_id', $user->id)),

                'exempted' => Tab::make('Exempted')
                    ->badge(Protocol::query()->where($statusColumn, $exemptedId)->where('user_id', $user->id)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)->where('user_id', $user->id)),

                'expedited' => Tab::make('Expedited')
                    ->badge(Protocol::query()->where($statusColumn, $expeditedId)->where('user_id', $user->id)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)->where('user_id', $user->id)),

                'fullboard' => Tab::make('Full Board')
                    ->badge(Protocol::query()->where($statusColumn, $fullboardId)->where('user_id', $user->id)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)->where('user_id', $user->id)),
            ];
        }

        return [
            'all' => Tab::make('Semua')
                ->badge(Protocol::count())
                ->query(fn (Builder $query) => $query),

            'proses' => Tab::make('Proses Pengajuan')
                ->badge(Protocol::where('status_id', 0)->count()) // Ganti 4 dengan ID untuk 'Proses Pengajuan'
                ->query(fn (Builder $query) => $query->where('status_id', 0)),

            'exempted' => Tab::make('Exempted')
                ->badge(Protocol::where('status_id', 1)->count())
                ->query(fn (Builder $query) => $query->where('status_id', 1)),

            'expedited' => Tab::make('Expedited')
                ->badge(Protocol::where('status_id', 3)->count())
                ->query(fn (Builder $query) => $query->where('status_id', 3)),

            'fullboard' => Tab::make('Full Board')
                ->badge(Protocol::where('status_id', 2)->count())
                ->query(fn (Builder $query) => $query->where('status_id', 2)),
        ];

    }

}
