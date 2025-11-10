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
        // Ganti 'status_id' jika nama kolom Anda berbeda
        $statusColumn = 'status_id';

        // GANTI ID (angka) di bawah ini sesuai database Anda
        $prosesId = null; // ❓ Ganti 4 dengan ID untuk 'Proses Pengajuan'
        $exemptedId = 1;
        $expeditedId = 3;
        $fullboardId = 2;


        return [
            'all' => Tab::make('Semua')
                ->badge(Protocol::query()->count()) // Jumlah total
                ->query(fn (Builder $query) => $query),

            'proses' => Tab::make('Proses Pengajuan')
                ->badge(Protocol::query()->where($statusColumn, $prosesId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $prosesId)),

            'exempted' => Tab::make('Exempted')
                ->badge(Protocol::query()->where($statusColumn, $exemptedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)),

            'expedited' => Tab::make('Expedited')
                ->badge(Protocol::query()->where($statusColumn, $expeditedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)),

            'fullboard' => Tab::make('Full Board')
                ->badge(Protocol::query()->where($statusColumn, $fullboardId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)),
        ];
    }

}
