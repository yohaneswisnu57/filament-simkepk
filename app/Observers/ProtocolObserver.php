<?php

namespace App\Observers;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Mail\ProtocolRevisionRequiredMail;
use App\Mail\ProtocolStatusUpdatedMail;
use App\Mail\ProtocolSubmittedMail;
use App\Models\Protocol;
use App\Models\ReviewerKelompok;
use App\Models\User;
use App\Services\ReviewerAssignmentNotifier;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProtocolObserver
{
    /**
     * Handle the Protocol "created" event.
     */
    public function created(Protocol $protocol): void
    {

        // ==========================================
        // 1. SUBMIT PROTOKOL (Peneliti -> Admin)
        // ==========================================

        $admins = User::role(['admin', 'super_admin'])->get()->unique('id');

        Notification::make()
            ->title('New Protocol Submission')
            ->body("Researcher {$protocol->User->name} has submitted a new protocol: \"{$protocol->perihal_pengajuan}\"")
            ->info()
            ->actions([
                Action::make('cek')
                    ->label('Check Protocol Completeness')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'admin')),
            ])
            ->sendToDatabase($admins);

        // 2. Notifikasi Email (Baru)
        // Kirim ke Peneliti
        if ($protocol->User && $protocol->User->email) {
            Mail::to($protocol->User->email)->queue(new ProtocolSubmittedMail($protocol));
        }

        // Kirim ke email tetap KEPK
        Mail::to('kepk.fk@ukwms.ac.id')->queue(new ProtocolSubmittedMail($protocol));
    }

    public function updating(Protocol $protocol): void
    {
        // ==========================================
        // SKENARIO: SYNC STATUS -> DECISION
        // ==========================================
        if ($protocol->isDirty('status_id')) {
            $newDecision = match ((int) $protocol->status_id) {
                1, 5 => 'Exempted',
                2 => 'Full Board',
                3 => 'Expedited',
                default => null,
            };

            if ($newDecision) {
                $protocol->fast_review_decision = $newDecision;
            }
        }

        // Generate certificate UUID if status is CERTIFICATE and it doesn't have one
        if ((int) $protocol->status_id === 5 && empty($protocol->certificate_uuid)) {
            $protocol->certificate_uuid = Str::uuid()->toString();
        }
    }

    /**
     * Handle the Protocol "updated" event.
     */
    public function updated(Protocol $protocol): void
    {
        // ==========================================
        // SKENARIO: ASSIGN KE KELOMPOK REVIEWER
        // ==========================================
        // Trigger: Kolom 'reviewer_kelompok_id' berubah dan tidak kosong
        if ($protocol->wasChanged('reviewer_kelompok_id')) {
            $oldGroupId = $protocol->getOriginal('reviewer_kelompok_id');

            // Beritahu anggota kelompok lama bahwa penugasan dicabut
            if (filled($oldGroupId)) {
                $this->notifyGroupUnassigned($protocol, (int) $oldGroupId);
            }

            // Skip group notification if this is a Fast Review (status 6)
            if (filled($protocol->reviewer_kelompok_id) && (int) $protocol->status_id !== 6) {
                $this->notifyGroupAssigned($protocol);
            }
        }
        // Protokol kembali dari Fast Review ke tahap review kelompok dengan kelompok yang sudah terisi
        elseif ($protocol->wasChanged('status_id')
            && filled($protocol->reviewer_kelompok_id)
            && (int) $protocol->getOriginal('status_id') === 6
            && in_array((int) $protocol->status_id, [2, 3, 4], true)
        ) {
            $this->notifyGroupAssigned($protocol);
        }

        // ==========================================
        // SKENARIO: FAST REVIEW ASSIGNMENT
        // ==========================================
        if ($protocol->isDirty('status_id') && $protocol->reviewer_kelompok_id) {

            $status = $protocol->statusReview;

            // NON-AKTIFKAN LOGIC OTOMATIS untuk 'Fast Review' jika menggunakan input manual dari Form.
            // if ($status && str_contains(strtolower($status->status_name), 'fast review')) {
            //     $this->assignFastReviewers($protocol);
            // }

            // TAPI: Jika update terjadi DI LUAR form edit (misal via API atau action button di list),
            // kita mungkin masih butuh fallback ini?
            // Untuk amannya, kita matikan dulu agar tidak menimpa pilihan manual admin.
        }

        if ($protocol->wasChanged('status_id')) {
            $statusId = (int) $protocol->status_id;

            // If status is REVISION REQUIRED (8)
            if ($statusId === 8) {
                // Notify Researcher
                Notification::make()
                    ->title('Revisi Protokol Diperlukan')
                    ->body("Protokol Anda \"{$protocol->perihal_pengajuan}\" membutuhkan revisi. Silakan periksa catatan dari Admin.")
                    ->warning()
                    ->actions([
                        Action::make('lihat')
                            ->label('Lihat Detail')
                            ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'user')),
                    ])
                    ->sendToDatabase($protocol->User);

                if ($protocol->User->email) {
                    Mail::to($protocol->User->email)
                        ->queue(new ProtocolRevisionRequiredMail($protocol));
                }
            }
            // Existing logic for Completed Final Review (2)
            elseif ($statusId === 2) {
                $admins = User::role('admin')->get();

                Notification::make()
                    ->title('Review Completed (Final)')
                    ->body("Review for protocol \"{$protocol->perihal_pengajuan}\" has been completed (Final Decision by Chairperson).")
                    ->success()
                    ->actions([
                        Action::make('lihat')
                            ->label('View Protocol')
                            ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'admin')),
                    ])
                    ->sendToDatabase($admins);

                // Kirim juga ke Peneliti bahwa protokolnya sudah selesai direview
                Notification::make()
                    ->title('Review Result Released')
                    ->body('Your protocol has been fully reviewed by the ethics committee.')
                    ->success()
                    ->sendToDatabase($protocol->User);
            }
            // Generic fallback for other status changes (e.g. going from Submission to Fast Review, or Fast Review to Expedited)
            else {
                // Notify Researcher about the status update
                $statusName = $protocol->statusReview->status_name ?? 'Diproses';

                Notification::make()
                    ->title('Status Protokol Diperbarui')
                    ->body("Status protokol Anda \"{$protocol->perihal_pengajuan}\" telah berubah menjadi: {$statusName}.")
                    ->info()
                    ->actions([
                        Action::make('lihat')
                            ->label('Lihat Detail')
                            ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'user')),
                    ])
                    ->sendToDatabase($protocol->User);

                if ($protocol->User->email) {
                    Mail::to($protocol->User->email)
                        ->queue(new ProtocolStatusUpdatedMail($protocol));
                }
            }
        }
    }

    protected function assignFastReviewers(Protocol $protocol)
    {
        $assignments = [];

        // 1. Ambil Ketua dari Kelompok Reviewer yang terhubung
        $kelompok = $protocol->assignedReviewerKelompok;
        if ($kelompok && $kelompok->ketua_user_id) {
            $assignments[$kelompok->ketua_user_id] = ['role_in_review' => 'Ketua'];
        }

        // 2. Ambil User dengan Role 'Sekertaris'
        // Jika hanya sekertaris yang ada di kelompok tersebut yang dimaksud, logika bisa disesuaikan.
        // Namun biasanya Sekertaris KEPK adalah role global.
        $sekertaris = User::role('sekertaris')->get();
        foreach ($sekertaris as $user) {
            // Hindari duplikasi jika sekertaris juga ketua
            if (! isset($assignments[$user->id])) {
                $assignments[$user->id] = ['role_in_review' => 'Sekertaris'];
            }
        }

        // 3. Sync ke tabel pivot
        // Menggunakan syncWithoutDetaching agar assignment manual lain tidak hilang (opsional)
        // Atau sync() untuk mereset dan memaksa hanya ketua & sekertaris
        // Disini kita gunakan syncWithoutDetaching untuk aman
        $protocol->reviewers()->syncWithoutDetaching($assignments);

        // Opsional: Kirim notifikasi ke mereka
        $userIds = array_keys($assignments);
        $usersToNotify = User::whereIn('id', $userIds)->get();

        Notification::make()
            ->title('Fast Review Assignment')
            ->body("You have been assigned for Fast Review on protocol: \"{$protocol->perihal_pengajuan}\"")
            ->danger() // Merah untuk mendesak
            ->actions([
                Action::make('review')
                    ->label('Review Now')
                    ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'reviewer')),
            ])
            ->sendToDatabase($usersToNotify);
    }

    /**
     * Handle the Protocol "deleted" event.
     */
    public function deleted(Protocol $protocol): void
    {
        //
    }

    /**
     * Handle the Protocol "restored" event.
     */
    public function restored(Protocol $protocol): void
    {
        //
    }

    /**
     * Handle the Protocol "force deleted" event.
     */
    public function forceDeleted(Protocol $protocol): void
    {
        //
    }

    /**
     * Kirim notifikasi database dan email penugasan ke seluruh penerima kelompok.
     */
    private function notifyGroupAssigned(Protocol $protocol): void
    {
        $kelompok = $protocol->assignedReviewerKelompok;

        if (! $kelompok) {
            return;
        }

        $reviewers = $kelompok->recipients();

        if ($reviewers->isNotEmpty()) {
            Notification::make()
                ->title('New Assignment for Group')
                ->body("Admin has assigned Group \"{$kelompok->nama_kelompok}\" to review protocol: \"{$protocol->perihal_pengajuan}\".")
                ->warning()
                ->actions([
                    Action::make('lihat')
                        ->label('View Protocol')
                        ->url(ProtocolResource::getUrl('view', ['record' => $protocol], panel: 'reviewer')),
                ])
                ->sendToDatabase($reviewers);
        }

        app(ReviewerAssignmentNotifier::class)->sendAssignmentEmails($protocol, $reviewers);
    }

    /**
     * Beritahu anggota kelompok lama bahwa penugasan protokol dialihkan.
     */
    private function notifyGroupUnassigned(Protocol $protocol, int $oldGroupId): void
    {
        $oldGroup = ReviewerKelompok::find($oldGroupId);

        if (! $oldGroup) {
            return;
        }

        $reviewers = $oldGroup->recipients();

        if ($reviewers->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('Group Assignment Removed')
            ->body("Protocol \"{$protocol->perihal_pengajuan}\" is no longer assigned to Group \"{$oldGroup->nama_kelompok}\".")
            ->info()
            ->sendToDatabase($reviewers);
    }
}
