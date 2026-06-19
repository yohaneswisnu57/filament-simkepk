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
use Filament\Support\Enums\Size;

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
                \Filament\Actions\ActionGroup::make([
                    Action::make('showQR')
                        ->label('Show QR')
                        ->tooltip('View Verification QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->color('secondary')
                        ->modalHeading('Verification QR Code')
                        ->modalContent(fn (Protocol $record) => new \Illuminate\Support\HtmlString('<div style="text-align:center; padding: 20px;">' . \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(route('certificates.verify', $record->certificate_uuid)) . '</div>'))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->visible(fn (Protocol $record): bool => ! empty($record->certificate_uuid)),
                    Action::make('generateCertificate')
                        ->label('Generate Certificate')
                        ->icon('heroicon-o-document-plus')
                        ->color('warning')
                        ->visible(fn (Protocol $record): bool => auth()->user()->hasRole(['admin', 'super_admin']) && $record->isReadyForCertificate())
                        ->form([
                            \Filament\Forms\Components\Repeater::make('members')
                                ->label('Member of Investigator')
                                ->schema([
                                    \Filament\Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->label('Name')
                                ])
                                ->default(fn(Protocol $record) => $record->certificate?->members ?? [])
                                ->addActionLabel('Add Member'),
                            \Filament\Forms\Components\TextInput::make('certificate_number')
                                ->label('Certificate Number / Ref')
                                ->default(fn(Protocol $record) => $record->certificate?->certificate_number)
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('institution_name')
                                ->label('Institution(s)/Place(s) of research')
                                ->default(fn(Protocol $record) => $record->certificate?->institution_name),
                            \Filament\Forms\Components\DatePicker::make('approval_date')
                                ->label('Date of Approval')
                                ->default(fn(Protocol $record) => $record->certificate?->approval_date ?? now())
                                ->required(),
                        ])
                        ->action(function (array $data, Protocol $record) {
                            $certificate = $record->certificate()->updateOrCreate(
                                ['protocol_id' => $record->id],
                                [
                                    'certificate_number' => $data['certificate_number'],
                                    'institution_name' => $data['institution_name'],
                                    'members' => $data['members'],
                                    'approval_date' => $data['approval_date'],
                                ]
                            );
                            
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', [
                                'protocol' => $record,
                                'certificate' => $certificate,
                            ]);
                            
                            $fileName = 'certificates/certificate_protocol_' . $record->id . '_' . time() . '.pdf';
                            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $pdf->output());
                            
                            $certificate->update(['file_path' => $fileName]);
                            $record->update(['certificate_file' => $fileName]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Certificate generated successfully')
                                ->success()
                                ->send();
                        }),
                    Action::make('previewCertificate')
                        ->label('Preview Certificate')
                        ->tooltip('View Certificate Document in Browser')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn (Protocol $record) => \Illuminate\Support\Facades\Storage::url($record->certificate_file))
                        ->openUrlInNewTab()
                        ->visible(fn (Protocol $record): bool => ! empty($record->certificate_file) && (
                            auth()->id() === $record->user_id
                            || auth()->user()->hasRole(['admin', 'super_admin'])
                        )),
                    Action::make('cetakCertificate')
                        ->label('Download Certificate')
                        ->tooltip('Download Certificate Document')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->visible(fn (Protocol $record): bool => ! empty($record->certificate_file) && (
                            auth()->id() === $record->user_id
                            || auth()->user()->hasRole(['admin', 'super_admin'])
                        ))
                        ->action(fn (Protocol $record) => \Illuminate\Support\Facades\Storage::disk('public')->download($record->certificate_file)),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()->requiresConfirmation(),
                ])->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(Size::Small)
                    ->color('primary')
                    ->button()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
