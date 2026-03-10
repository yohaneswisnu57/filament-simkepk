<?php

namespace App\Filament\Resources\Protocols\Schemas;

use App\Models\Protocol;
use App\Models\StatusReview;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProtocolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ──────────────────────────────────────────────────
                // SECTION 1: Informasi Dasar Protokol
                // ──────────────────────────────────────────────────
                Section::make('Informasi Protokol')
                    ->columns(2)
                    ->schema([
                        TextInput::make('perihal_pengajuan')
                            ->label('Perihal Pengajuan')
                            ->required()
                            ->columnSpanFull(),

                        Select::make('jenis_protocol')
                            ->label('Jenis Protokol')
                            ->options([
                                'Manusia' => 'Manusia',
                                'Hewan' => 'Hewan',
                            ])
                            ->searchable()
                            ->required(),

                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->tel()
                            ->minLength(10)
                            ->maxLength(15)
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->validationMessages([
                                'telRegex' => 'Contact person harus berupa nomor telepon yang valid.',
                            ])
                            ->nullable(),

                        DatePicker::make('tanggal_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->native(false)
                            ->displayFormat('D d/m/Y')
                            ->closeOnDateSelection(true)
                            ->default(now())
                            ->readOnly(),

                        // Status — hanya admin yang bisa ubah
                        Select::make('status_id')
                            ->label('Status')
                            ->relationship(name: 'StatusReview', titleAttribute: 'status_name')
                            ->live()
                            ->visible(fn (): bool => auth()->user()->hasRole(['admin', 'super_admin'])),

                        // Created By — display only
                        Select::make('user_id')
                            ->label('Dibuat Oleh')
                            ->relationship('user', 'name')
                            ->default(fn (): int => auth()->id())
                            ->disabled()
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->visible(fn (): bool => auth()->user()->hasRole(['admin', 'super_admin'])),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 2: Review Timeline & Assignment
                // Hanya tampil untuk admin dan sekertaris
                // ──────────────────────────────────────────────────
                Section::make('Review Timeline & Assignment')
                    ->columns(2)
                    ->visible(fn (): bool => auth()->user()->hasRole(['admin', 'super_admin', 'sekertaris']))
                    ->schema([
                        DatePicker::make('tgl_mulai_review')
                            ->label('Tanggal Mulai Review')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->closeOnDateSelection(true)
                            ->before('tgl_selesai_review'),

                        DatePicker::make('tgl_selesai_review')
                            ->label('Tanggal Selesai Review')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->format('Y-m-d')
                            ->closeOnDateSelection(true)
                            ->afterOrEqual('tgl_mulai_review'),

                        // Assign ke Kelompok Reviewer (Regular Review)
                        // Disembunyikan saat status = Fast Review
                        Select::make('reviewer_kelompok_id')
                            ->label('Assign ke Kelompok Reviewer')
                            ->relationship('assignedReviewerKelompok', 'nama_kelompok')
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->visible(function ($get): bool {
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return true;
                                }
                                $status = StatusReview::find($statusId);

                                return ! ($status && str_contains(strtolower($status->status_name), 'fast review'));
                            }),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 3: Fast Review Assignment
                // Hanya tampil saat status = Fast Review (untuk admin/sekertaris)
                // ──────────────────────────────────────────────────
                Section::make('Fast Review — Pilih Reviewer')
                    ->columns(2)
                    ->description('Pilih 2 reviewer untuk Fast Review. Kombinasi: Ketua + Sekertaris, atau Sekertaris + Sekertaris.')
                    ->visible(function ($get): bool {
                        if (! auth()->user()->hasRole(['admin', 'super_admin', 'sekertaris'])) {
                            return false;
                        }
                        $statusId = $get('status_id');
                        if (! $statusId) {
                            return false;
                        }
                        $status = StatusReview::find($statusId);

                        return $status && str_contains(strtolower($status->status_name), 'fast review');
                    })
                    ->schema([
                        // Reviewer 1 — pilih dari role 'reviewer' (Ketua)
                        Select::make('fast_review_ketua_id')
                            ->label('Reviewer 1 (Ketua)')
                            ->options(User::role('reviewer')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(function ($get): bool {
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            ->afterStateHydrated(function (Select $component, ?Protocol $record): void {
                                if (! $record) {
                                    return;
                                }
                                $ketua = $record->reviewers()->wherePivot('role_in_review', 'Ketua')->first();
                                $component->state($ketua?->id);
                            })
                            ->dehydrated(false),

                        // Reviewer 2 — pilih dari role 'sekertaris'
                        Select::make('fast_review_secretary_id')
                            ->label('Reviewer 2 (Sekertaris)')
                            ->options(User::role('sekertaris')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(function ($get): bool {
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            ->afterStateHydrated(function (Select $component, ?Protocol $record): void {
                                if (! $record) {
                                    return;
                                }
                                $sekertaris = $record->reviewers()->wherePivot('role_in_review', 'Sekertaris')->first();
                                $component->state($sekertaris?->id);
                            })
                            ->dehydrated(false),
                    ]),

                // ──────────────────────────────────────────────────
                // SECTION 4: File Pendukung
                // ──────────────────────────────────────────────────
                Section::make('File Pendukung')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('uploadpernyataan')
                            ->label('Upload Pernyataan')
                            ->disk('public')
                            ->directory('uploadpernyataan')
                            ->preserveFilenames()
                            ->required()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(2048)
                            ->helperText('Format: PDF / DOCX. Ukuran maksimal: 2MB.')
                            ->validationMessages([
                                'acceptedFileTypes' => 'File harus berformat PDF atau DOCX.',
                                'max' => 'Ukuran file tidak boleh melebihi 2MB.',
                            ]),

                        FileUpload::make('buktipembayaran')
                            ->label('Bukti Pembayaran')
                            ->disk('public')
                            ->directory('buktipembayaran')
                            ->preserveFilenames()
                            ->required()
                            ->acceptedFileTypes([
                                'image/jpg',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->maxSize(2048)
                            ->helperText('Format: JPG / PNG. Ukuran maksimal: 2MB.')
                            ->validationMessages([
                                'acceptedFileTypes' => 'File harus berformat JPG atau PNG.',
                                'max' => 'Ukuran file tidak boleh melebihi 2MB.',
                            ]),
                    ]),

            ]);
    }
}
