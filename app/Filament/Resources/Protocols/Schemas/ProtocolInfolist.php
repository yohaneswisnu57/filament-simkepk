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
use Filament\Schemas\Components\Tabs;
use Spatie\Activitylog\Models\Activity;

class ProtocolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Protocol Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        // ──────────────────────────────────────────────────
                        // TAB 1: Main Info
                        // ──────────────────────────────────────────────────
                        Tabs\Tab::make('Main Information')
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                Section::make('Protocol Information')
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

                                Section::make('Supporting Documents')
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
                            ]),

                        // ──────────────────────────────────────────────────
                        // TAB 2: Review & Assignment
                        // ──────────────────────────────────────────────────
                        Tabs\Tab::make('Review & Assignment')
                            ->icon(Heroicon::UserGroup)
                            ->schema([
                                Section::make('Review Timeline')
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

                                Section::make('Fast Review Status')
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
                            ]),

                        // ──────────────────────────────────────────────────
                        // TAB 3: Comments
                        // ──────────────────────────────────────────────────
                        Tabs\Tab::make('Notes & Comments')
                            ->icon(Heroicon::ChatBubbleLeftRight)
                            ->schema([
                                CommentsEntry::make('comments')
                                    ->label('')
                                    ->hideSubscribers(true)
                                    ->unsubscribeColor('danger'),
                            ]),

                        // ──────────────────────────────────────────────────
                        // TAB 4: Activity Log (NEW)
                        // ──────────────────────────────────────────────────
                        Tabs\Tab::make('History')
                            ->icon(Heroicon::Clock)
                            ->visible(fn (): bool => auth()->user()->hasRole(['admin', 'super_admin', 'sekertaris']))
                            ->schema([
                                RepeatableEntry::make('activities')
                                    ->label('Changes Timeline')
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label('Action')
                                            ->weight('bold')
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                        
                                        TextEntry::make('causer.name')
                                            ->label('By')
                                            ->placeholder('System'),

                                        TextEntry::make('created_at')
                                            ->label('Date')
                                            ->dateTime('d M Y, H:i'),

                                        TextEntry::make('description')
                                            ->label('Aktivitas & Perubahan')
                                            ->placeholder('Dokumen Dibuat')
                                            ->formatStateUsing(function ($record) {
                                                $changes = $record->getChangesAttribute();
                                                $attributes = $changes['attributes'] ?? [];
                                                $old = $changes['old'] ?? [];
                                                
                                                if (empty($attributes)) {
                                                    return match($record->description) {
                                                        'created' => '📋 Protokol baru telah didaftarkan.',
                                                        default => ucfirst($record->description),
                                                    };
                                                }

                                                $statusMap = [
                                                    1 => 'EXEMPTED', 2 => 'FULL BOARD', 3 => 'EXPEDITED',
                                                    4 => 'ON REVIEW', 5 => 'CERTIFICATE', 6 => 'FAST REVIEW', 
                                                    7 => 'SUBMISSION',
                                                ];

                                                $fieldMap = [
                                                    'status_id' => 'Status Protokol',
                                                    'fast_review_decision' => 'Keputusan Review',
                                                    'reviewer_kelompok_id' => 'Kelompok Reviewer',
                                                    'perihal_pengajuan' => 'Judul Penelitian',
                                                    'contact_person' => 'Kontak',
                                                    'certificate_name' => 'Nama di Sertifikat',
                                                ];

                                                $output = [];
                                                foreach ($attributes as $key => $value) {
                                                    if (in_array($key, ['updated_at', 'id', 'certificate_name_changes'])) continue;

                                                    $label = $fieldMap[$key] ?? ucfirst(str_replace('_', ' ', $key));
                                                    $oldValue = $old[$key] ?? null;

                                                    // Format values
                                                    if ($key === 'status_id') {
                                                        $value = $statusMap[$value] ?? $value;
                                                        $oldValue = $oldValue ? ($statusMap[$oldValue] ?? $oldValue) : 'N/A';
                                                    }

                                                    if ($oldValue === null || $oldValue === 'n/a') {
                                                        $output[] = "• **{$label}** disetel ke: **{$value}**";
                                                    } else {
                                                        $output[] = "• **{$label}** berubah dari \"{$oldValue}\" menjadi **{$value}**";
                                                    }
                                                }
                                                return count($output) > 0 ? implode("\n", $output) : 'Update data rutin.';
                                            })
                                            ->markdown()
                                            ->wrap()
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
