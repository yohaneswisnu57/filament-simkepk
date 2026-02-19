<?php

namespace App\Filament\Resources\Protocols\Schemas;

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
                Section::make('Information Protocol')
                    // ->label('Informasi Protocol')
                    ->columns(2)
                    ->schema([
                        TextInput::make('perihal_pengajuan')
                            ->label('Concerning')
                            ->required(),
                        Select::make('jenis_protocol')
                            ->label('Subject Protocol')
                            ->options([
                                'Manusia' => 'Manusia',
                                'Hewan' => 'Hewan',
                            ])
                            ->searchable()
                            ->required(),
                        TextInput::make('contact_person')
                            ->tel()
                            ->minLength(10)
                            ->maxLength(15)
                            ->label('Contact Person')
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->validationMessages([
                                'telRegex' => 'The contact person must be a valid phone number.',
                            ])
                            ->nullable(),
                        DatePicker::make('tanggal_pengajuan')
                            ->label('Submission Date')
                            ->native(false)
                            ->displayFormat('D d/m/Y')
                            ->closeOnDateSelection(true)
                            ->default(now())
                            ->readOnly(),
                        Select::make('status_id')
                            ->label('Status')
                            ->default('null')
                            // ->required()
                            ->relationship(name: 'StatusReview', titleAttribute: 'status_name')
                            ->live() // Make it reactive
                            ->afterStateUpdated(function ($state, $set) {
                                // Reset fields if status changes
                                if ($state != 2) {
                                    // Assuming 2 is Fast Review. Ideally fetch dynamically or use constant.
                                    // But for now let's rely on blade or text check logic if possible,
                                    // but in backend ID is safest if we know it.
                                }
                            })
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Created By')
                            ->default(fn () => auth()->id()) // Default user yang login saat create
                            ->disabled() // Supaya tidak bisa diubah manual (opsional)
                            // KUNCI PERBAIKANNYA DISINI:
                            // Hanya kirim data ke database saat proses 'create'.
                            // Saat 'edit', field ini akan diabaikan oleh query update.
                            ->dehydrated(fn ($operation) => $operation === 'create')
                            ->formatStateUsing(function ($state, ?string $operation) {
                                // Jika mode edit dan state kosong (jarang terjadi), kembalikan state asli
                                // Jika mode create, default() di atas yang akan menangani
                                return $record?->user_id ?? $state;
                            })
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole(['admin', 'super_admin'])),
                        // ->default(fn () => 1), // Set default status_id to 1 (e.g., 'PENDING')
                    ]),

                Section::make('Review Timeline')
                    // ->label('Review Timeline')
                    ->columns(2)
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
                    ->schema([
                        DatePicker::make('tgl_mulai_review')
                            ->label('Date Start Review')
                            ->native(false)
                            ->displayFormat('Y/m/d')
                            ->format('Y/m/d')
                            ->closeOnDateSelection(true)
                            ->before('tgl_selesai_review')
                            // ->required() // Conditional required handled below or strictly required
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                        DatePicker::make('tgl_selesai_review')
                            ->label('Date End Review')
                            ->native(false)
                            ->displayFormat('Y/m/d')
                            ->closeOnDateSelection(true)
                            ->afterOrEqual('tgl_mulai_review')
                            ->format('Y/m/d')
                            // ->required()
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),

                        // Regular Review Assignment
                        Select::make('reviewer_kelompok_id')
                            ->label('Assign to Reviewer Group')
                            ->relationship('assignedReviewerKelompok', 'nama_kelompok')
                            ->nullable()
                            // Sembunyikan jika status adalah 'Fast Review'
                            // Kita asumsikan kita cek Text dari selected relationship jika live?
                            // Susah cek text relation di client side filamant tanpa query.
                            // Kita gunakan query sederhana.
                            ->visible(function ($get) {
                                if (! auth()->user()->hasRole(['super_admin', 'admin', 'sekertaris'])) {
                                    return false;
                                }

                                // Cek status ID. Jika ID merujuk ke 'Fast Review', return false.
                                // Kita ambil nama status dari ID
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return true;
                                }
                                $status = \App\Models\StatusReview::find($statusId);
                                if ($status && str_contains(strtolower($status->status_name), 'fast review')) {
                                    return false;
                                }

                                return true;
                            }),

                        // FAST REVIEW: Select Ketua
                        Select::make('fast_review_ketua_id')
                            ->label('Assign Ketua (Fast Review)')
                            // Removed 'ketua' role as it might not exist in Spatie roles table yet causing 500 error.
                            // Assuming any 'reviewer' can be assigned as Ketua for fast review, OR just use 'reviewer'.
                            ->options(\App\Models\User::role('reviewer')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(function ($get) {
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = \App\Models\StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            ->visible(function ($get) {
                                if (! auth()->user()->hasRole(['super_admin', 'admin', 'sekertaris'])) {
                                    return false;
                                }
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = \App\Models\StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            // Load existing data if editing
                            ->afterStateHydrated(function (Select $component, ?\App\Models\Protocol $record) {
                                if (! $record) {
                                    return;
                                }
                                // Ambil ketua dari pivot
                                $ketua = $record->reviewers()->wherePivot('role_in_review', 'Ketua')->first();
                                if ($ketua) {
                                    $component->state($ketua->id);
                                }
                            })
                            ->dehydrated(false), // Jangan simpan ke kolom 'fast_review_ketua_id' di tabel protocols (karena tidak ada)

                        // FAST REVIEW: Select Sekertaris
                        Select::make('fast_review_secretary_id')
                            ->label('Assign Sekertaris (Fast Review)')
                            ->options(\App\Models\User::role('sekertaris')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(function ($get) {
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = \App\Models\StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            ->visible(function ($get) {
                                if (! auth()->user()->hasRole(['super_admin', 'admin', 'sekertaris'])) {
                                    return false;
                                }
                                $statusId = $get('status_id');
                                if (! $statusId) {
                                    return false;
                                }
                                $status = \App\Models\StatusReview::find($statusId);

                                return $status && str_contains(strtolower($status->status_name), 'fast review');
                            })
                            // Load existing data if editing
                            ->afterStateHydrated(function (Select $component, ?\App\Models\Protocol $record) {
                                if (! $record) {
                                    return;
                                }
                                // Ambil sekertaris dari pivot
                                $sekertaris = $record->reviewers()->wherePivot('role_in_review', 'Sekertaris')->first();
                                if ($sekertaris) {
                                    $component->state($sekertaris->id);
                                }
                            })
                            ->dehydrated(false),  // Jangan simpan ke kolom ini di tabel protocols
                    ]),

                Section::make('Supporting Files')
                    // ->label('File Pendukung')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('uploadpernyataan')
                            ->label('Upload Statement')
                            ->required()
                            ->preserveFilenames()
                            ->disk('public')
                            ->directory('uploadpernyataan')
                            ->acceptedFileTypes([
                                'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ]) // Opsional: Batasi hanya PDF/Docx
                            ->maxSize(2048) // <--- Batasan 3MB (3072 KB)
                            ->helperText('Maximum file size: 3MB.')
                            ->validationMessages([
                                'acceptedFileTypes' => 'The file must be a type of: PDF, DOCX.',
                                'max' => 'The file size must not exceed 2MB.',
                            ]),
                        FileUpload::make('buktipembayaran')
                            ->label('Upload Proof of Payment')
                            ->acceptedFileTypes([
                                'application/jpg', 'application/jpeg', 'application/png',
                            ])
                            ->helperText('Accepted file types: JPG, JPEG, PNG. Maximum file size: 2MB.')
                            ->validationMessages([
                                'acceptedFileTypes' => 'The file must be a type of: JPG, JPEG, PNG.',
                                'max' => 'The file size must not exceed 2MB.',
                            ])
                            ->maxSize(2048)
                            ->required()
                            ->preserveFilenames()
                            ->disk('public')
                            ->directory('buktipembayaran'),
                    ]),

            ]);
    }
}
