<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class ProtocolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ──────────────────────────────────────────────────
                // SECTION 1: Protocol Information
                // ──────────────────────────────────────────────────
                Section::make('Protocol Information')
                    ->icon(Heroicon::DocumentText)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('perihal_pengajuan')
                            ->label('Research Title')
                            ->columnSpanFull(),

                        TextEntry::make('jenis_protocol')
                            ->label('Protocol Type')
                            ->placeholder('-'),

                        TextEntry::make('contact_person')
                            ->label('Contact Person')
                            ->placeholder('-'),

                        TextEntry::make('tanggal_pengajuan')
                            ->label('Submission Date')
                            ->dateTime('D, d M Y'),

                        TextEntry::make('user.name')
                            ->label('Submitted By'),

                        TextEntry::make('StatusReview.status_name')
                            ->label('Status')
                            ->badge()
                            ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                                'FULL BOARD' => 'danger',
                                'EXEMPTED' => 'success',
                                'EXPEDITED' => 'info',
                                'FAST REVIEW' => 'warning',
                                default => 'gray',
                            })
                            ->placeholder('-'),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 2: Supporting Documents
                // ──────────────────────────────────────────────────
                Section::make('Supporting Documents')
                    ->icon(Heroicon::PaperClip)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('uploadpernyataan')
                            ->label('Statement Letter')
                            ->beforeContent(Icon::make(Heroicon::DocumentArrowDown))
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                            ->action(
                                \Filament\Actions\Action::make('downloadPernyataan')
                                    ->label('Download File')
                                    ->icon(Heroicon::ArrowDownTray)
                                    ->color('info')
                                    ->action(fn (Protocol $record) => Storage::disk('public')->download($record->uploadpernyataan))
                                    ->visible(fn (Protocol $record): bool => ! empty($record->uploadpernyataan))
                            ),

                        TextEntry::make('buktipembayaran')
                            ->label('Proof of Payment')
                            ->beforeContent(Icon::make(Heroicon::DocumentArrowDown))
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                            ->action(
                                \Filament\Actions\Action::make('downloadBukti')
                                    ->label('Download File')
                                    ->icon(Heroicon::ArrowDownTray)
                                    ->color('info')
                                    ->action(fn (Protocol $record) => Storage::disk('public')->download($record->buktipembayaran))
                                    ->visible(fn (Protocol $record): bool => ! empty($record->buktipembayaran))
                            ),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 3: Review Timeline
                // ──────────────────────────────────────────────────
                Section::make('Review Timeline')
                    ->icon(Heroicon::CalendarDays)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('tgl_mulai_review')
                            ->label('Start Date')
                            ->date('D, d M Y')
                            ->placeholder('-'),

                        TextEntry::make('tgl_selesai_review')
                            ->label('End Date')
                            ->date('D, d M Y')
                            ->placeholder('-'),

                        TextEntry::make('assignedReviewerKelompok.nama_kelompok')
                            ->label('Reviewer Group')
                            ->placeholder('-'),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 4: Fast Review Status
                // ──────────────────────────────────────────────────
                Section::make('Fast Review Status')
                    ->icon(Heroicon::ClipboardDocumentList)
                    ->columns(1)
                    ->visible(fn (Protocol $record): bool => str_contains(strtolower($record->statusReview?->status_name ?? ''), 'fast review')
                        && auth()->user()->hasRole(['admin', 'super_admin'])
                    )
                    ->schema([
                        RepeatableEntry::make('reviewers')
                            ->label('Assigned Reviewers')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name'),

                                TextEntry::make('pivot.role_in_review')
                                    ->label('Role')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'Ketua' => 'info',
                                        'Sekertaris' => 'primary',
                                        default => 'gray',
                                    }),

                                TextEntry::make('pivot.feedback_status')
                                    ->label('Submission Status')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'submitted' => '✓ Submitted',
                                        default => '● Waiting',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'submitted' => 'success',
                                        default => 'warning',
                                    }),
                            ]),

                        TextEntry::make('fast_review_decision')
                            ->label('Final Decision')
                            ->badge()
                            ->placeholder('Waiting for all reviewers to submit...')
                            ->color(fn (?string $state): string => match ($state) {
                                'Exempted' => 'success',
                                'Full Board' => 'danger',
                                'Pending' => 'warning',
                                default => 'gray',
                            }),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 5: Notes & Comments
                // ──────────────────────────────────────────────────
                Section::make('Notes & Comments')
                    ->icon(Heroicon::ChatBubbleLeftRight)
                    ->columnSpanFull()
                    ->schema([
                        CommentsEntry::make('comments')
                            ->label('')
                            ->hideSubscribers(true)
                            ->unsubscribeColor('danger'),
                    ])
                    ->visible(fn (Model $record): bool => $record instanceof Protocol),

            ]);
    }
}
