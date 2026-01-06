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
                            ->label('Type Protocol')
                            ->options([
                                'Manusia' => 'Manusia',
                                'Hewan' => 'Hewan',
                            ])
                            ->searchable()
                            ->required(),
                        DatePicker::make('tanggal_pengajuan')
                            ->label('Submission Date')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->format('Y/m/d')
                            ->closeOnDateSelection(true)
                            ->required(),
                        Select::make('status_id')
                            ->label('Status')
                            ->default('null')
                            // ->required()
                            ->relationship(name: 'StatusReview', titleAttribute: 'status_name')
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                        Select::make('user_id')
                            ->relationship('User', 'name')
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
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                        DatePicker::make('tgl_selesai_review')
                            ->label('Date End Review')
                            ->native(false)
                            ->displayFormat('Y/m/d')
                            ->closeOnDateSelection(true)
                            ->afterOrEqual('tgl_mulai_review')
                            ->format('Y/m/d')
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                        Select::make('reviewer_kelompok_id')
                            ->label('Assign to Reviewer Groups')
                            ->relationship('assignedReviewerKelompok', 'name') // Ganti 'nama_kelompok' dengan kolom nama di ReviewerKelompok
                            ->searchable()
                            ->preload()
                            ->nullable()
                            // Hanya 'super_admin' atau role tertentu yang bisa meng-assign
                            ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin', 'sekertaris'])),
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
                                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ]) // Opsional: Batasi hanya PDF/Docx
                            ->maxSize(3072) // <--- Batasan 3MB (3072 KB)
                            ->validationMessages([
                                'max' => 'Ukuran file terlalu besar. Maksimal hanya 3MB.',
                            ]),
                        FileUpload::make('buktipembayaran')
                            ->label('Upload Proof of Payment')
                            ->required()
                            ->preserveFilenames()
                            ->disk('public')
                            ->directory('buktipembayaran'),
                ]),
            ]);
        }
}
