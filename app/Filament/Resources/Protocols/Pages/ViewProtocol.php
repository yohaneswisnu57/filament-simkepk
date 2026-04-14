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
            // ACTION 1: Submit Verdict (for reviewer)
            // ──────────────────────────────────────────────────
            Action::make('submitVerdict')
                ->label('Submit Verdict')
                ->icon(Heroicon::ClipboardDocumentCheck)
                ->color('warning')
                ->modalHeading('Submit Fast Review Verdict')
                ->modalDescription('Provide your decision for this protocol. Decisions cannot be changed after submission.')
                ->visible(function (): bool {
                    return $this->record->reviewers()
                        ->where('users.id', auth()->id())
                        ->wherePivot('feedback_status', 'pending')
                        ->exists();
                })
                ->form([
                    Textarea::make('comment')
                        ->label('Notes / Comments')
                        ->placeholder('Write your review notes...')
                        ->required()
                        ->rows(5),

                    Select::make('verdict')
                        ->label('Decision')
                        ->options([
                            'Exempted' => '✅ Exempted (Pass)',
                            'Expedited' => '🔄 Expedited (Review Kelompok)',
                            'Full Board' => '⚠️ Full Board (Further Review Needed)',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $protocol = $this->record;

                    // 1. Save review with verdict
                    Review::create([
                        'protocol_id' => $protocol->id,
                        'user_id' => auth()->id(),
                        'comment' => $data['comment'],
                        'verdict' => $data['verdict'],
                        'submitted_at' => now(),
                    ]);

                    // Tambahkan juga ke relasi "comments" agar muncul di kolom Notes & Comments
                    $protocol->comments()->create([
                        'author_type' => get_class(auth()->user()),
                        'author_id' => auth()->id(),
                        'body' => "**Verdict: {$data['verdict']}**\n\n{$data['comment']}",
                    ]);

                    // 2. Update pivot feedback_status → 'submitted'
                    $protocol->reviewers()->updateExistingPivot(auth()->id(), [
                        'feedback_status' => 'submitted',
                    ]);

                    // 3. Run Decision Engine
                    app(FastReviewDecisionService::class)->evaluate($protocol->fresh());

                    Notification::make()
                        ->title('Verdict submitted successfully')
                        ->success()
                        ->send();

                    $this->refreshFormData(['fast_review_decision']);
                }),

            // ──────────────────────────────────────────────────
            // ACTION 2: Issue Certificate (for admin)
            // ──────────────────────────────────────────────────
            Action::make('terbitkanCertificate')
                ->label('Issue Certificate')
                ->icon(Heroicon::DocumentCheck)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Issue Exempted Certificate')
                ->modalDescription('All reviewers have agreed on Exempted. Confirm to issue the certificate.')
                ->visible(fn (): bool => $this->record->fast_review_decision === 'Exempted'
                    && auth()->user()->hasRole(['admin', 'super_admin'])
                )
                ->action(function (): void {
                    $exemptedStatus = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%exempted%'])->first();

                    $this->record->update([
                        'status_id' => $exemptedStatus?->id ?? $this->record->status_id,
                    ]);

                    Notification::make()
                        ->title('Certificate issued successfully')
                        ->body("Protocol \"{$this->record->perihal_pengajuan}\" status updated to Exempted.")
                        ->success()
                        ->send();

                    // Notify researcher
                    Notification::make()
                        ->title('🎉 Certificate Issued!')
                        ->body('Congratulations! Your protocol has been declared Exempted and the certificate has been issued.')
                        ->success()
                        ->sendToDatabase($this->record->User);

                    $this->refreshFormData(['status_id']);
                }),

            // ──────────────────────────────────────────────────
            // ACTION 3: Print Certificate (for owner & admin)
            // ──────────────────────────────────────────────────
            Action::make('cetakCertificate')
                ->label('Print Certificate')
                ->icon(Heroicon::Printer)
                ->color('info')
                ->modalHeading('Print Certificate')
                ->modalDescription('Please enter your full name to ensure it is printed correctly on the certificate.')
                ->visible(fn (): bool => str_contains(
                    strtolower($this->record->statusReview?->status_name ?? ''),
                    'exempted'
                ) && (
                    auth()->id() === $this->record->user_id
                    || auth()->user()->hasRole(['admin', 'super_admin'])
                ))
                ->schema([
                    TextInput::make('nama_lengkap')
                        ->label('Full Name')
                        ->placeholder('Enter full name as per official documents')
                        ->default(fn () => auth()->user()->name)
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $url = route('certificates.protocol', [
                        'protocol' => $this->record->id,
                        'nama' => $data['nama_lengkap'],
                    ]);

                    // Dispatch browser event
                    $this->dispatch('open-url', url: $url);
                }),
        ];
    }
}
