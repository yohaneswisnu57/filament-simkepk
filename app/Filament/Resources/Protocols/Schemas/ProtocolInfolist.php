<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use Filament\Actions\Action;
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
                // SECTION 1: Informasi Protokol
                // ──────────────────────────────────────────────────
                Section::make('Informasi Protokol')
                    ->icon(Heroicon::DocumentText)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('perihal_pengajuan')
                            ->label('Perihal Pengajuan')
                            ->columnSpanFull(),

                        TextEntry::make('jenis_protocol')
                            ->label('Jenis Protokol')
                            ->placeholder('-'),

                        TextEntry::make('contact_person')
                            ->label('Contact Person')
                            ->placeholder('-'),

                        TextEntry::make('tanggal_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('D, d M Y'),

                        TextEntry::make('user.name')
                            ->label('Diajukan Oleh'),

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
                // SECTION 2: Dokumen Pendukung
                // ──────────────────────────────────────────────────
                Section::make('Dokumen Pendukung')
                    ->icon(Heroicon::PaperClip)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('uploadpernyataan')
                            ->label('Surat Pernyataan')
                            ->beforeContent(Icon::make(Heroicon::DocumentArrowDown))
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                            ->action(
                                Action::make('downloadPernyataan')
                                    ->label('Unduh File')
                                    ->icon(Heroicon::ArrowDownTray)
                                    ->color('info')
                                    ->action(fn (Protocol $record) => Storage::disk('public')->download($record->uploadpernyataan))
                                    ->visible(fn (Protocol $record): bool => ! empty($record->uploadpernyataan))
                            ),

                        TextEntry::make('buktipembayaran')
                            ->label('Bukti Pembayaran')
                            ->beforeContent(Icon::make(Heroicon::DocumentArrowDown))
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                            ->action(
                                Action::make('downloadBukti')
                                    ->label('Unduh File')
                                    ->icon(Heroicon::ArrowDownTray)
                                    ->color('info')
                                    ->action(fn (Protocol $record) => Storage::disk('public')->download($record->buktipembayaran))
                                    ->visible(fn (Protocol $record): bool => ! empty($record->buktipembayaran))
                            ),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 3: Timeline Review
                // ──────────────────────────────────────────────────
                Section::make('Timeline Review')
                    ->icon(Heroicon::CalendarDays)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('tgl_mulai_review')
                            ->label('Tanggal Mulai')
                            ->date('D, d M Y')
                            ->placeholder('-'),

                        TextEntry::make('tgl_selesai_review')
                            ->label('Tanggal Selesai')
                            ->date('D, d M Y')
                            ->placeholder('-'),

                        TextEntry::make('assignedReviewerKelompok.nama_kelompok')
                            ->label('Kelompok Reviewer')
                            ->placeholder('-'),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 4: Fast Review Status
                // Hanya tampil saat status Fast Review (untuk admin/sekertaris)
                // ──────────────────────────────────────────────────
                Section::make('Fast Review Status')
                    ->icon(Heroicon::ClipboardDocumentList)
                    ->columns(1)
                    ->visible(fn (Protocol $record): bool => str_contains(strtolower($record->statusReview?->status_name ?? ''), 'fast review')
                        && auth()->user()->hasRole(['admin', 'super_admin', 'sekertaris'])
                    )
                    ->schema([
                        RepeatableEntry::make('reviewers')
                            ->label('Reviewer yang Ditugaskan')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama'),

                                TextEntry::make('pivot.role_in_review')
                                    ->label('Peran')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'Ketua' => 'info',
                                        'Sekertaris' => 'primary',
                                        default => 'gray',
                                    }),

                                TextEntry::make('pivot.feedback_status')
                                    ->label('Status Submit')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'submitted' => '✓ Sudah Submit',
                                        default => '● Menunggu',
                                    })
                                    ->color(fn (?string $state): string => match ($state) {
                                        'submitted' => 'success',
                                        default => 'warning',
                                    }),
                            ]),

                        TextEntry::make('fast_review_decision')
                            ->label('Keputusan Akhir')
                            ->badge()
                            ->placeholder('Menunggu semua reviewer submit...')
                            ->color(fn (?string $state): string => match ($state) {
                                'Exempted' => 'success',
                                'Full Board' => 'danger',
                                'Pending' => 'warning',
                                default => 'gray',
                            }),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 5: Catatan & Komentar
                // ──────────────────────────────────────────────────
                Section::make('Catatan & Komentar')
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
