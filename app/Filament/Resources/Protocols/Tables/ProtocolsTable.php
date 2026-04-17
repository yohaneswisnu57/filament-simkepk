<?php

namespace App\Filament\Resources\Protocols\Tables;

use App\Models\Protocol;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ProtocolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Research Title')
                    ->searchable()
                    ->wrap()
                    ->limit(60),

                TextColumn::make('user.name')
                    ->label('Researcher')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('jenis_protocol')
                    ->label('Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Manusia' => 'info',
                        'Hewan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Submission Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('statusReview.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                        'FULL BOARD' => 'danger',
                        'EXEMPTED' => 'success',
                        'CERTIFICATE' => 'success',
                        'EXPEDITED' => 'info',
                        'FAST REVIEW' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('fast_review_decision')
                    ->label('Decision')
                    ->badge()
                    ->placeholder('-')
                    ->color(fn (?string $state): string => match ($state) {
                        'Exempted' => 'success',
                        'Full Board' => 'danger',
                        'Pending' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('tgl_mulai_review')
                    ->label('Start Review')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgl_selesai_review')
                    ->label('End Review')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('tanggal_pengajuan')
                    ->label('Filter by Submission Date')
                    ->form([
                        DatePicker::make('tanggal_pengajuan')
                            ->label('Submission Date')
                            ->native(false),
                    ])
                    ->query(fn ($query, $data) => $query->when(
                        $data['tanggal_pengajuan'],
                        fn ($q, $date) => $q->whereDate('tanggal_pengajuan', $date)
                    )),

            ])
            ->recordActions([
                Action::make('cetakCertificate')
                    ->label('Print')
                    ->tooltip('Print Certificate')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->modalHeading('Print Certificate')
                    ->modalDescription('Please enter your full name to ensure it is printed correctly on the certificate.')
                    ->visible(fn (Protocol $record): bool => $record->isReadyForCertificate() && (
                        auth()->id() === $record->user_id
                        || auth()->user()->hasRole(['admin', 'super_admin'])
                    )
                    )
                    ->form([
                        TextInput::make('nama_lengkap')
                            ->label('Full Name')
                            ->placeholder('Enter full name as per official documents')
                            ->default(fn (Protocol $record) => $record->certificate_name ?? auth()->user()->name)
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (Protocol $record) => ! auth()->user()->hasRole(['admin', 'super_admin'])
                                && ! $record->canResearcherUpdateName()
                            )
                            ->helperText(function (Protocol $record) {
                                if (auth()->user()->hasRole(['admin', 'super_admin'])) {
                                    return "Admin can update name anytime. Current changes: {$record->certificate_name_changes}";
                                }
                                $remaining = 2 - $record->certificate_name_changes;

                                return $remaining > 0
                                    ? "Remaining name update attempts: {$remaining}"
                                    : 'No more update attempts remaining. Please contact admin if correction is needed.';
                            }),
                    ])
                    ->action(function (Protocol $record, array $data, $livewire): void {
                        $isAdmin = auth()->user()->hasRole(['admin', 'super_admin']);
                        $newName = $data['nama_lengkap'];

                        // Jika nama berubah atau status belum CERTIFICATE, kita update database
                        if ($newName !== $record->certificate_name || $record->status_id !== 5) {
                            $updateData = [
                                'certificate_name' => $newName,
                                'status_id' => 5, // Mark as CERTIFICATE automatically
                            ];
                            if (! $isAdmin) {
                                $updateData['certificate_name_changes'] = $record->certificate_name_changes + 1;
                            }
                            $record->update($updateData);
                        }

                        $url = route('certificates.protocol', [
                            'protocol' => $record->id,
                        ]);

                        $livewire->dispatch('open-url', url: $url);
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
