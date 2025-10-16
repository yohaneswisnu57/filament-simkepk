<?php

namespace App\Filament\Resources\Protocols\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Laravel\Prompts\Table;

class ProtocolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('perihal_pengajuan')
                    ->label('Perihal Pengajuan')
                    ->required(),
                TextInput::make('jenis_protocol')
                    ->label('Jenis Protocol')
                    ->required(),
                DatePicker::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->native(false)
                    ->displayFormat('Y-m-d')
                    ->format('Y/m/d')
                    ->closeOnDateSelection(true)
                    ->required(),
                Select::make('status_id')
                    ->label('Status Pengajuan')
                    ->required()
                    ->relationship(name: 'StatusReview', titleAttribute: 'status_name'),
                FileUpload::make('uploadpernyataan')
                    ->label('Upload Pernyataan')
                    ->required()
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory('uploadpernyataan'),
                FileUpload::make('buktipembayaran')
                    ->label('Upload Bukti Pembayaran')
                    ->required()
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory('buktipembayaran'),
                TextInput::make('user_id')
                    ->label('User ID')
                    ->required()
                    ->readOnly(true)
                    ->hidden()
                    ->default(Auth::id()),
                DatePicker::make('tgl_mulai_review')
                    ->label('Tanggal Mulai Review')
                    ->native(false)
                    ->displayFormat('Y/m/d')
                    ->format('Y/m/d')
                    ->closeOnDateSelection(true)
                    ->before('tgl_selesai_review')
                    ->required()
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                DatePicker::make('tgl_selesai_review')
                    ->label('Tanggal Selesai Review')
                    ->native(false)
                    ->displayFormat('Y/m/d')
                    ->closeOnDateSelection(true)
                    ->afterOrEqual('tgl_mulai_review')
                    ->format('Y/m/d')
                    ->required()
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),

                // Repeater::make('documents')
                //     ->table([
                //         TableColumn::make('Nama Dokumen'),
                //         TableColumn::make('Jenis Dokumen'),
                //         TableColumn::make('Upload Dokumen'),
                //         TableColumn::make('Aksi'),
                //     ])
                //     ->schema([
                //         TextInput::make('namadokumen')
                //             ->label('Nama Dokumen')
                //             ->placeholder('Masukkan nama dokumen')
                //             ->required(),
                //         Select::make('jenisdokumen')
                //             ->label('Jenis Dokumen')
                //             ->placeholder('Pilih jenis dokumen')
                //             ->options([
                //                 'docx' => 'Docx',
                //                 'pdf' => 'PDF',
                //             ])
                //             ->required(),
                //         FileUpload::make('path')
                //             ->label('Upload Dokumen')
                //             ->required()
                //             ->disk('public')
                //             ->directory('dokumen_pendukung')
                //             ->maxSize(10240) // Maksimum ukuran file 10MB
                //             ->preserveFilenames(),
                //     ])
                //     ->columns(4)
                //     ->deleteAction(
                //         fn (Action $action) => $action->requiresConfirmation(),
                //     )
        ]);
    }


}
