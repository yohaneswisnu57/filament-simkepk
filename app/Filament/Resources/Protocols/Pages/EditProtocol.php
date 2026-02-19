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
        // ❌ DIHAPUS: $data['user_id'] = auth()->user()->id;
        // Alasan: user_id adalah "Created By" dan tidak boleh berubah saat edit
        // Sudah di-handle di ProtocolForm dengan ->dehydrated(fn ($operation) => $operation === 'create')

        $user = auth()->user();
        $assignedKelompokId = $this->record->reviewer_kelompok_id;

        // LOGIKA: Jika User adalah KETUA dari kelompok yang sedang ditugaskan
        if ($assignedKelompokId && $user->isKetuaDariKelompok($assignedKelompokId)) {
            // Paksa status menjadi DONE (misal ID 1 adalah code untuk 'Selesai/Approved')
            // $data['status_id'] = 1; // Hati-hati menimpa status manual. Uncomment jika logic bisnis mengharuskan.

            // Opsional: Simpan tanggal selesai otomatis
            $data['tgl_selesai_review'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $protocol = $this->record;
        $data = $this->data; // Akses data form yang disubmit via ->data

        // Logic Manual Assignment untuk Fast Review (UPDATE)
        // Kita gunakan sync pada user tertentu saja atau detach specific roles?
        // Agar aman, kita cek: Jika ada input, kita update pivot-nya.

        if (isset($data['fast_review_ketua_id']) && $data['fast_review_ketua_id']) {
            // 1. Hapus Ketua lama jika ada
            $protocol->reviewers()->wherePivot('role_in_review', 'Ketua')->detach();

            // 2. Assign Ketua baru
            $protocol->reviewers()->attach($data['fast_review_ketua_id'], ['role_in_review' => 'Ketua']);
        }

        if (isset($data['fast_review_secretary_id']) && $data['fast_review_secretary_id']) {
            // 1. Hapus Sekertaris lama
            $protocol->reviewers()->wherePivot('role_in_review', 'Sekertaris')->detach();

            // 2. Assign Sekertaris baru (skip jika sama dg ketua baru)
            if ($data['fast_review_secretary_id'] != ($data['fast_review_ketua_id'] ?? null)) {
                $protocol->reviewers()->attach($data['fast_review_secretary_id'], ['role_in_review' => 'Sekertaris']);
            }
        }
    }
}
