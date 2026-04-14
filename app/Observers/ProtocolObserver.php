<?php

namespace App\Observers;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Mail\ProtocolSubmittedMail;
use App\Mail\ReviewAssignmentMail;
use App\Models\Protocol;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

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
                    ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);

        if ($admins->isEmpty()) {
            return;
        }
        // 2. Notifikasi Email (Baru)
        // Kirim ke setiap admin
        foreach ($admins as $admin) {
            // Pastikan admin punya email valid
            if ($admin->email) {
                Mail::to($admin->email)->queue(new ProtocolSubmittedMail($protocol));
            }
        }
        // }
    }

    /**
     * Handle the Protocol "updated" event.
     */
    /**
     * Handle the Protocol "updated" event.
     */
    public function updated(Protocol $protocol): void
    {
        // ==========================================
        // SKENARIO: ASSIGN KE KELOMPOK REVIEWER
        // ==========================================
        // Trigger: Kolom 'reviewer_kelompok_id' berubah dan tidak kosong
        if ($protocol->wasChanged('reviewer_kelompok_id') && ! empty($protocol->reviewer_kelompok_id)) {

            // 1. Ambil Kelompok ID yang baru di-assign
            $groupId = $protocol->reviewer_kelompok_id;

            // 2. Ambil Nama Kelompok (Optional, untuk pesan notifikasi lebih jelas)
            // Pastikan Anda punya relasi 'reviewerKelompok' di model Protocol
            $groupName = $protocol->reviewerKelompok->name ?? 'Kelompok Terpilih';

            // 3. Cari SEMUA User yang menjadi anggota kelompok tersebut
            // Kita filter User berdasarkan reviewer_kelompok_id yang sama
            $reviewers = User::where('reviewer_kelompok_id', $groupId)
                ->get();

            // 4. Kirim Notifikasi ke semua anggota kelompok
            if ($reviewers->count() > 0) {
                Notification::make()
                    ->title('New Assignment for Group')
                    ->body("Admin has assigned Group \"{$groupName}\" to review protocol: \"{$protocol->perihal_pengajuan}\".")
                    ->warning()
                    ->actions([
                        Action::make('lihat')
                            ->label('View Protocol')
                            ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
                    ])
                    ->sendToDatabase($reviewers);
            }

            // 5. Kirim Email ke Reviewer
            foreach ($reviewers as $reviewer) {
                if ($reviewer->email) {
                    Mail::to($reviewer->email)
                        ->queue(new ReviewAssignmentMail($protocol));
                }
            }
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

        if ($protocol->wasChanged('status_id') && $protocol->statusReview->id == 2) {

            $admins = User::role('admin')->get();

            Notification::make()
                ->title('Review Completed (Final)')
                ->body("Review for protocol \"{$protocol->perihal_pengajuan}\" has been completed (Final Decision by Chairperson).")
                ->success()
                ->actions([
                    Action::make('lihat')
                        ->label('View Protocol')
                        ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
                ])
                ->sendToDatabase($admins);

            // Kirim juga ke Peneliti bahwa protokolnya sudah selesai direview
            Notification::make()
                ->title('Review Result Released')
                ->body('Your protocol has been fully reviewed by the ethics committee.')
                ->success()
                ->sendToDatabase($protocol->User);
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
                    ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
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
}
