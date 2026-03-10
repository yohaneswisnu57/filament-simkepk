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
        $user = auth()->user();
        $assignedKelompokId = $this->record->reviewer_kelompok_id;

        if ($assignedKelompokId && $user->isKetuaDariKelompok($assignedKelompokId)) {
            $data['tgl_selesai_review'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $protocol = $this->record;
        $data = $this->data;

        $ketuaId = $data['fast_review_ketua_id'] ?? null;
        $sekertarisId = $data['fast_review_secretary_id'] ?? null;

        // Jika tidak ada perubahan reviewer Fast Review, skip
        if (! $ketuaId && ! $sekertarisId) {
            return;
        }

        // 1. Hapus reviewer Fast Review lama (Ketua & Sekertaris dari pivot)
        $protocol->reviewers()->wherePivotIn('role_in_review', ['Ketua', 'Sekertaris'])->detach();

        // 2. Attach reviewer baru dengan feedback_status = 'pending'
        if ($ketuaId) {
            $protocol->reviewers()->attach($ketuaId, [
                'role_in_review' => 'Ketua',
                'feedback_status' => 'pending',
            ]);
        }

        if ($sekertarisId && $sekertarisId != $ketuaId) {
            $protocol->reviewers()->attach($sekertarisId, [
                'role_in_review' => 'Sekertaris',
                'feedback_status' => 'pending',
            ]);
        }

        // 3. Set fast_review_decision = 'Pending' karena reviewer baru di-assign
        $protocol->update(['fast_review_decision' => 'Pending']);
    }
}
