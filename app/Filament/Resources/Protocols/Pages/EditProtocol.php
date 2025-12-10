<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProtocol extends EditRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->user()->id;
        $user = auth()->user();

        $assignedKelompokId = $this->record->reviewer_kelompok_id;

        // LOGIKA UTAMA:
        // Jika User adalah KETUA dari kelompok yang sedang ditugaskan
        if ($assignedKelompokId && $user->isKetuaDariKelompok($assignedKelompokId)) {

            // Paksa status menjadi DONE (misal ID 1 adalah code untuk 'Selesai/Approved')
            $data['status_id'] = 1;

            // Opsional: Simpan tanggal selesai otomatis
            $data['tgl_selesai_review'] = now();
        }

        return $data;
    }
}
