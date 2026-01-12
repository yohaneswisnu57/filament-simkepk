<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewSubmittedMail;

class ViewProtocol extends ViewRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        // return [
        //     EditAction::make(),
        // ];

        // 👇 3. Tambahkan seluruh metode ini
        return [
            EditAction::make(), // Tombol Edit (jika ada)

            // Tombol untuk menambah review
            Action::make('addReview')
                ->label('Beri Review/Komentar')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('info')

                // Hanya tampilkan jika user adalah 'reviewer' (sesuaikan nama role)
                ->visible(fn () => auth()->user()->hasRole(['reviewer', 'admin', 'super_admin', 'sekertaris']))

                // Ini akan memunculkan form modal
                ->form([
                    RichEditor::make('comment')
                        ->label('Komentar Review')
                        ->required()
                        ->minLength(3),
                ])


                // Logika saat tombol "Submit" di modal ditekan
                ->action(function (array $data) {

                    // $this->record adalah data Protocol yang sedang dibuka
                    $this->record->reviews()->create([
                        'comment' => $data['comment'],
                        'user_id' => auth()->id(),
                    ]);

                    // 👇 2. LOGIKA EMAIL NOTIFIKASI DISINI
                    // =====================================

                    // Ambil data peneliti (User pemilik protokol)
                    $reviewerEmail = $this->record->user;

                    // Cek apakah peneliti punya email valid
                    if ($reviewerEmail && $reviewerEmail->email) {

                        // Kirim Email
                        // Parameter 2: Nama Reviewer.
                        // Gunakan 'Anggota Penelaah' agar anonim (Blind Review),
                        // atau gunakan auth()->user()->name jika ingin transparan.
                        Mail::to($reviewerEmail->email)
                            ->send(new ReviewSubmittedMail($this->record, auth()->user()->name));
                    }
                    // =====================================

                    $this->record->refresh();

                    // Kirim notifikasi sukses
                    Notification::make()
                        ->title('Review berhasil disimpan')
                        ->success()
                        ->send();
                })
                // Mengirim sinyal refresh setelah action selesai
                ->after(function () {
                    $this->dispatch('refresh-reviews-table');
                }),
        ];

    }
}
