<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;

class CreateProtocol extends CreateRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $protocol = $this->record;
        $data = $this->data;

        $ketuaId = $data['fast_review_ketua_id'] ?? null;
        $sekertarisId = $data['fast_review_secretary_id'] ?? null;

        if (! $ketuaId && ! $sekertarisId) {
            return;
        }

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

        // Set 'Pending' hanya jika status adalah Fast Review
        if ((int) $protocol->status_id === 6) {
            $protocol->update(['fast_review_decision' => 'Pending']);

            // Kirim Notifikasi khusus ke Penilai Fast Review
            $assignedIds = array_filter([$ketuaId, $sekertarisId]);
            if (!empty($assignedIds)) {
                $reviewersToNotify = User::whereIn('id', $assignedIds)->get();
                Notification::make()
                    ->title('Penugasan Fast Review Baru')
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
