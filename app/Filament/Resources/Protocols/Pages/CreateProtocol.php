<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateProtocol extends CreateRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        return $data;
    }

    // protected function afterCreate():void
    // {
    //     $protocol = $this->record;

    //     // 1. Ambil semua User yang punya role 'admin' (menggunakan Spatie)
    //     $admins = User::hasRole('admin');

    //     // 2. Kirim Notifikasi
    //     Notification::make()
    //         ->title('Protokol Baru Masuk')
    //         ->body("Peneliti {$protocol->user->name} mengajukan judul: \"{$protocol->judul}\"")
    //         ->icon('heroicon-o-document-text')
    //         ->info()
    //         ->actions([
    //             Action::make('view')
    //                 ->label('Lihat Protokol')
    //                 ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
    //         ])
    //         ->sendToDatabase($admins);

    // }


}


