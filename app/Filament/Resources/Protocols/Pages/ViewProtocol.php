<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Review;
use App\Models\StatusReview;
use App\Services\FastReviewDecisionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewProtocol extends ViewRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => auth()->user()->hasRole(['admin', 'super_admin', 'sekertaris'])),

            // ──────────────────────────────────────────────────
            // ACTION 1: Submit Verdict (untuk reviewer)
            // Muncul hanya jika user adalah reviewer yang di-assign
            // DAN belum submit (feedback_status = 'pending')
            // ──────────────────────────────────────────────────
            Action::make('submitVerdict')
                ->label('Submit Verdict')
                ->icon(Heroicon::ClipboardDocumentCheck)
                ->color('warning')
                ->modalHeading('Submit Fast Review Verdict')
                ->modalDescription('Berikan keputusan Anda untuk protokol ini. Keputusan tidak dapat diubah setelah disubmit.')
                ->visible(function (): bool {
                    return $this->record->reviewers()
                        ->where('users.id', auth()->id())
                        ->wherePivot('feedback_status', 'pending')
                        ->exists();
                })
                ->form([
                    Textarea::make('comment')
                        ->label('Catatan / Komentar')
                        ->placeholder('Tuliskan catatan telaah Anda...')
                        ->required()
                        ->rows(5),

                    Select::make('verdict')
                        ->label('Keputusan')
                        ->options([
                            'Exempted' => '✅ Exempted (Lolos)',
                            'Full Board' => '⚠️ Full Board (Perlu Telaah Lanjut)',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $protocol = $this->record;

                    // 1. Simpan review dengan verdict
                    Review::create([
                        'protocol_id' => $protocol->id,
                        'user_id' => auth()->id(),
                        'comment' => $data['comment'],
                        'verdict' => $data['verdict'],
                        'submitted_at' => now(),
                    ]);

                    // 2. Update pivot feedback_status → 'submitted'
                    $protocol->reviewers()->updateExistingPivot(auth()->id(), [
                        'feedback_status' => 'submitted',
                    ]);

                    // 3. Jalankan Decision Engine
                    app(FastReviewDecisionService::class)->evaluate($protocol->fresh());

                    Notification::make()
                        ->title('Verdict berhasil disubmit')
                        ->success()
                        ->send();

                    $this->refreshFormData(['fast_review_decision']);
                }),

            // ──────────────────────────────────────────────────
            // ACTION 2: Terbitkan Certificate (untuk admin)
            // Muncul hanya saat fast_review_decision = 'Exempted'
            // ──────────────────────────────────────────────────
            Action::make('terbitkanCertificate')
                ->label('Terbitkan Certificate')
                ->icon(Heroicon::DocumentCheck)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Terbitkan Certificate Exempted')
                ->modalDescription('Semua reviewer telah menyetujui Exempted. Konfirmasi untuk menerbitkan certificate.')
                ->visible(fn (): bool => $this->record->fast_review_decision === 'Exempted'
                    && auth()->user()->hasRole(['admin', 'super_admin'])
                )
                ->action(function (): void {
                    $exemptedStatus = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%exempted%'])->first();

                    $this->record->update([
                        'status_id' => $exemptedStatus?->id ?? $this->record->status_id,
                    ]);

                    Notification::make()
                        ->title('Certificate berhasil diterbitkan')
                        ->body("Protokol \"{$this->record->perihal_pengajuan}\" telah berstatus Exempted.")
                        ->success()
                        ->send();

                    // Notifikasi ke peneliti
                    Notification::make()
                        ->title('🎉 Certificate Diterbitkan!')
                        ->body('Selamat! Protokol Anda telah dinyatakan Exempted dan certificate telah diterbitkan.')
                        ->success()
                        ->sendToDatabase($this->record->User);

                    $this->refreshFormData(['status_id']);
                }),

            // ──────────────────────────────────────────────────
            // ACTION 3: Cetak Sertifikat (untuk pemilik protokol & admin)
            // Muncul hanya saat status = Exempted
            // User diminta input nama lengkap sebelum mencetak
            // ──────────────────────────────────────────────────
            Action::make('cetakCertificate')
                ->label('Cetak Sertifikat')
                ->icon(Heroicon::Printer)
                ->color('info')
                ->modalHeading('Cetak Sertifikat')
                ->modalDescription('Masukkan nama lengkap Anda untuk memastikan nama tercetak dengan benar di sertifikat.')
                ->visible(fn (): bool => str_contains(
                    strtolower($this->record->statusReview?->status_name ?? ''),
                    'exempted'
                ) && (
                    auth()->id() === $this->record->user_id
                    || auth()->user()->hasRole(['admin', 'super_admin'])
                ))
                ->schema([
                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->placeholder('Masukkan nama lengkap sesuai dokumen resmi')
                        ->default(fn () => auth()->user()->name)
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $url = route('certificates.protocol', [
                        'protocol' => $this->record->id,
                        'nama' => $data['nama_lengkap'],
                    ]);

                    // Dispatch browser event — ditangkap Alpine.js listener di AdminPanelProvider
                    $this->dispatch('open-url', url: $url);
                }),
        ];
    }
}
