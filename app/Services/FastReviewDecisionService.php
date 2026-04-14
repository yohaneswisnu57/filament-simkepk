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

        // 4. Ada >=1 "Expedited" -> eskalasi ke Expedited (Review Kelompok)
        if ($verdicts->contains('Expedited')) {
            $this->escalateToExpedited($protocol);

            return;
        }

        // 5. Semua "Exempted" → siap certificate
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
            ->title('⚠️ Fast Review: Escalated to Full Board')
            ->body("Protocol \"{$protocol->perihal_pengajuan}\" has been escalated as a reviewer decided on Full Board.")
            ->warning()
            ->actions([
                Action::make('lihat')
                    ->label('View Protocol')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        // Notifikasi Peneliti
        Notification::make()
            ->title('Fast Review Result: Full Board')
            ->body('Your protocol will be escalated to Full Board Review.')
            ->warning()
            ->sendToDatabase($protocol->User);
    }

    private function escalateToExpedited(Protocol $protocol): void
    {
        $expeditedStatus = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%expedited%'])->first();

        $protocol->update([
            'fast_review_decision' => 'Expedited',
            'status_id' => $expeditedStatus?->id ?? $protocol->status_id,
        ]);

        // Notifikasi Admin
        $admins = User::role(['admin', 'super_admin'])->get();

        Notification::make()
            ->title('⚠️ Fast Review: Escalated to Expedited (Group Review)')
            ->body("Protocol \"{$protocol->perihal_pengajuan}\" has been reassigned to Group Review.")
            ->warning()
            ->actions([
                Action::make('lihat')
                    ->label('View Protocol')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        // Notifikasi Peneliti
        Notification::make()
            ->title('Fast Review Result: Expedited')
            ->body('Your protocol will proceed to the Group Review (Expedited) track.')
            ->warning()
            ->sendToDatabase($protocol->User);

        // Notifikasi ke Reviewer Kelompok (Email + Database)
        if ($protocol->reviewer_kelompok_id) {
            $kelompok = $protocol->assignedReviewerKelompok;
            if ($kelompok && $kelompok->users) {
                // Notifikasi Database
                Notification::make()
                    ->title('📌 New Assignment: Expedited Review')
                    ->body("Protocol \"{$protocol->perihal_pengajuan}\" has been escalated to your Review Group (Expedited).")
                    ->info()
                    ->actions([
                        Action::make('lihat')
                            ->label('Review Protocol')
                            ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
                    ])
                    ->sendToDatabase($kelompok->users);

                // Notifikasi Email
                foreach ($kelompok->users as $reviewer) {
                    if ($reviewer->email) {
                        \Illuminate\Support\Facades\Mail::to($reviewer->email)
                            ->queue(new \App\Mail\ReviewAssignmentMail($protocol));
                    }
                }
            }
        }
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
            ->title('✅ Fast Review: All Reviewers Agreed on Exempted')
            ->body("Protocol \"{$protocol->perihal_pengajuan}\" is ready for certificate issuance.")
            ->success()
            ->actions([
                Action::make('lihat')
                    ->label('View & Issue Certificate')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        // Notifikasi Peneliti
        Notification::make()
            ->title('Fast Review Completed')
            ->body('All reviewers have completed the review of your protocol.')
            ->success()
            ->sendToDatabase($protocol->User);
    }
}
