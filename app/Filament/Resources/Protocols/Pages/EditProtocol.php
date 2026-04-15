<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;

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

        // Ambil penilai saat ini dari database untuk perbandingan
        $currentKetuaId = $protocol->reviewers()->wherePivot('role_in_review', 'Ketua')->first()?->id;
        $currentSekertarisId = $protocol->reviewers()->wherePivot('role_in_review', 'Sekertaris')->first()?->id;

        // Jika penilai tidak berubah, jangan lakukan apa-apa (jaga integritas keputusan/review yang ada)
        if ((int) $ketuaId === (int) $currentKetuaId && (int) $sekertarisId === (int) $currentSekertarisId) {
            return;
        }

        // Jika tidak ada data penilai di form, skip
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

        // 3. Set fast_review_decision = 'Pending' HANYA jika status adalah 'Fast Review' (ID 6)
        // Reset ini hanya terjadi karena penilai baru saja di-assign ulang
        if ((int) $protocol->status_id === 6) {
            $protocol->update(['fast_review_decision' => 'Pending']);

            // 4. Kirim Notifikasi khusus ke Penilai Fast Review
            $assignedIds = array_filter([$ketuaId, $sekertarisId]);
            if (!empty($assignedIds)) {
                $reviewersToNotify = User::whereIn('id', $assignedIds)->get();
                Notification::make()
                    ->title('Penugasan Fast Review')
                    ->body("Anda telah ditugaskan sebagai penilai Fast Review untuk protokol: \"{$protocol->perihal_pengajuan}\"")
                    ->danger()
                    ->actions([
                        Action::make('cek')
                            ->label('Buka Protokol')
                            ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
                    ])
                    ->sendToDatabase($reviewersToNotify);
            }
        }
    }
}
