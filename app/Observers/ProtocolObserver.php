<?php

namespace App\Observers;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Protocol;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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

        $admins = User::role(['admin','super_admin'])->get();

        Notification::make()
            ->title('Pengajuan Protokol Baru')
            ->body("Peneliti {$protocol->User->name} mengajukan protokol baru: \"{$protocol->perihal_pengajuan}\"")
            ->info()
            ->actions([
                Action::make('cek')
                    ->label('Cek Kelengkapan Pengajuan Protocol')
                    ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
            ])
            ->sendToDatabase($admins);
        // }
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
        if ($protocol->wasChanged('reviewer_kelompok_id') && !empty($protocol->reviewer_kelompok_id)) {

            // 1. Ambil Kelompok ID yang baru di-assign
            $groupId = $protocol->reviewer_kelompok_id;

            // 2. Ambil Nama Kelompok (Optional, untuk pesan notifikasi lebih jelas)
            // Pastikan Anda punya relasi 'reviewerKelompok' di model Protocol
            $groupName = $protocol->reviewerKelompok->nama_kelompok ?? 'Kelompok Terpilih';

            // 3. Cari SEMUA User yang menjadi anggota kelompok tersebut
            // Kita filter User berdasarkan reviewer_kelompok_id yang sama
            $reviewers = User::where('reviewer_kelompok_id', $groupId)
                            ->get();

            // 4. Kirim Notifikasi ke semua anggota kelompok
            if ($reviewers->count() > 0) {
                Notification::make()
                    ->title('Tugas Baru untuk Kelompok')
                    ->body("Admin menugaskan Kelompok \"{$groupName}\" untuk menelaah protokol: \"{$protocol->judul}\".")
                    ->warning()
                    ->actions([
                        Action::make('lihat')
                            ->label('Lihat Protokol')
                            ->url(ProtocolResource::getUrl('edit', ['record' => $protocol])),
                    ])
                    ->sendToDatabase($reviewers);
            }
        }

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
