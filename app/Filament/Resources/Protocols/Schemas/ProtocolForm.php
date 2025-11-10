<?php

namespace App\Filament\Resources\Protocols\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Laravel\Prompts\Table;

class ProtocolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Protocol')
                    // ->label('Informasi Protocol')
                    ->columns(2)
                    ->schema([
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
                            ->default('null')
                            // ->required()
                            ->relationship(name: 'StatusReview', titleAttribute: 'status_name')
                            ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')),
                            // ->default(fn () => 1), // Set default status_id to 1 (e.g., 'PENDING')
                    ]),

                Section::make('Review Timeline')
                    // ->label('Review Timeline')
                    ->columns(2)
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
                    ->schema([
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


                    ]),





                Section::make('File Pendukung')
                    // ->label('File Pendukung')
                    ->columns(1)
                    ->schema([
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
                    ]),

        ]);
    }


}
