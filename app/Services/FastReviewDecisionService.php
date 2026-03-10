<?php

namespace App\Services;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Protocol;
use App\Models\Review;
use App\Models\StatusReview;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class FastReviewDecisionService
{
    /**
     * Evaluasi keputusan Fast Review setelah seorang reviewer submit verdict.
     * Dipanggil setiap kali Review baru dibuat untuk protokol Fast Review.
     */
    public function evaluate(Protocol $protocol): void
    {
        // 1. Cek apakah masih ada reviewer yang belum submit
        $pendingCount = $protocol->reviewers()
            ->wherePivot('feedback_status', 'pending')
            ->count();

        if ($pendingCount > 0) {
            // Masih ada yang belum submit, tidak ada perubahan
            return;
        }

        // 2. Semua sudah submit — ambil semua verdict mereka
        $assignedIds = $protocol->reviewers()->pluck('users.id');

        $verdicts = Review::where('protocol_id', $protocol->id)
            ->whereIn('user_id', $assignedIds)
            ->whereNotNull('verdict')
            ->pluck('verdict');

        if ($verdicts->isEmpty()) {
            return;
        }

        // 3. Decision logic:
        //    Ada ≥1 "Full Board" → eskalasi
        if ($verdicts->contains('Full Board')) {
            $this->escalateToFullBoard($protocol);

            return;
        }

        // 4. Semua "Exempted" → siap certificate
        if ($verdicts->every(fn (string $v): bool => $v === 'Exempted')) {
            $this->markAsExempted($protocol);
        }
    }

    private function escalateToFullBoard(Protocol $protocol): void
    {
        $fullBoardStatus = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%full board%'])->first();

        $protocol->update([
            'fast_review_decision' => 'Full Board',
            'status_id' => $fullBoardStatus?->id ?? $protocol->status_id,
        ]);

        // Notifikasi Admin
        $admins = User::role(['admin', 'super_admin'])->get();

        Notification::make()
            ->title('⚠️ Fast Review: Eskalasi ke Full Board')
            ->body("Protokol \"{$protocol->perihal_pengajuan}\" → ada reviewer yang memutuskan Full Board.")
            ->warning()
            ->actions([
                Action::make('lihat')
                    ->label('Lihat Protokol')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        // Notifikasi Peneliti
        Notification::make()
            ->title('Hasil Fast Review: Full Board')
            ->body('Protokol Anda akan masuk ke jalur Full Board Review.')
            ->warning()
            ->sendToDatabase($protocol->User);
    }

    private function markAsExempted(Protocol $protocol): void
    {
        $protocol->update([
            'fast_review_decision' => 'Exempted',
            // status_id TIDAK berubah otomatis — admin yang klik "Terbitkan Certificate"
        ]);

        // Notifikasi Admin → munculkan tombol certificate
        $admins = User::role(['admin', 'super_admin'])->get();

        Notification::make()
            ->title('✅ Fast Review: Semua Reviewer Setuju Exempted')
            ->body("Protokol \"{$protocol->perihal_pengajuan}\" siap diterbitkan certificate-nya.")
            ->success()
            ->actions([
                Action::make('lihat')
                    ->label('Lihat & Terbitkan Certificate')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        // Notifikasi Peneliti
        Notification::make()
            ->title('Fast Review Selesai')
            ->body('Semua reviewer telah menyelesaikan telaah protokol Anda.')
            ->success()
            ->sendToDatabase($protocol->User);
    }
}
