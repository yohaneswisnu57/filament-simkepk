<?php

namespace App\Filament\Resources\Users\Actions;

use App\Models\ReviewerKelompok;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ImportReviewerAction
{
    public static function make(): Action
    {
        return Action::make('importReviewer')
            ->label('Import Reviewer')
            ->icon(Heroicon::ArrowUpTray)
            ->color('success')
            ->modalHeading('Import Data Reviewer dari CSV')
            ->modalDescription('Upload file CSV untuk menambahkan banyak reviewer sekaligus. Setiap user yang diimpor akan otomatis mendapatkan role **reviewer** dan **panel_reviewer**.')
            ->modalWidth('lg')
            ->modalFooterActionsAlignment('right')
            ->form([
                FileUpload::make('csv_file')
                    ->label('File CSV')
                    ->disk('local')
                    ->directory('livewire-tmp')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->maxSize(2048)
                    ->required()
                    ->helperText('Format kolom: name, email, password, reviewer_kelompok_id. Maksimal 2MB.'),
            ])
            ->modalSubmitActionLabel('Proses Import')
            ->action(function (array $data): void {
                $filePath = storage_path('app/private/'.$data['csv_file']);

                if (! file_exists($filePath)) {
                    Notification::make()
                        ->title('File tidak ditemukan')
                        ->body('File CSV gagal diunggah atau sudah dihapus.')
                        ->danger()
                        ->send();

                    return;
                }

                $handle = fopen($filePath, 'r');
                if ($handle === false) {
                    Notification::make()
                        ->title('Gagal membaca file')
                        ->danger()
                        ->send();

                    return;
                }

                // Read header row
                $header = fgetcsv($handle, 0, ',');
                if ($header === false) {
                    fclose($handle);
                    Notification::make()
                        ->title('File CSV kosong atau format header salah')
                        ->danger()
                        ->send();

                    return;
                }

                // Normalize header (trim whitespace)
                $header = array_map('trim', array_map('strtolower', $header));

                // Validate required columns
                $requiredColumns = ['name', 'email', 'password'];
                $missingColumns = array_diff($requiredColumns, $header);
                if (! empty($missingColumns)) {
                    fclose($handle);
                    Notification::make()
                        ->title('Kolom wajib tidak ditemukan')
                        ->body('Kolom yang hilang: '.implode(', ', $missingColumns))
                        ->danger()
                        ->send();

                    return;
                }

                $validKelompokIds = ReviewerKelompok::pluck('id')->toArray();
                $successCount = 0;
                $skipCount = 0;
                $errorRows = [];
                $lineNumber = 1; // header is line 1

                while (($row = fgetcsv($handle, 0, ',')) !== false) {
                    $lineNumber++;

                    // Skip completely empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map row to associative array using header
                    if (count($row) < count($header)) {
                        $row = array_pad($row, count($header), '');
                    }
                    $rowData = array_combine($header, array_slice($row, 0, count($header)));

                    // Trim values
                    $rowData = array_map('trim', $rowData);

                    // Validate row
                    $validator = Validator::make($rowData, [
                        'name' => 'required|string|max:255',
                        'email' => 'required|email|max:255',
                        'password' => 'required|string|min:6',
                    ]);

                    if ($validator->fails()) {
                        $errorRows[] = "Baris {$lineNumber}: ".implode(', ', $validator->errors()->all());

                        continue;
                    }

                    // Check for duplicate email
                    if (User::withTrashed()->where('email', $rowData['email'])->exists()) {
                        $skipCount++;

                        continue;
                    }

                    // Determine kelompok
                    $kelompokId = null;
                    if (isset($rowData['reviewer_kelompok_id']) && $rowData['reviewer_kelompok_id'] !== '') {
                        $kelompokId = (int) $rowData['reviewer_kelompok_id'];
                        if (! in_array($kelompokId, $validKelompokIds)) {
                            $errorRows[] = "Baris {$lineNumber}: Kelompok ID '{$rowData['reviewer_kelompok_id']}' tidak ditemukan.";

                            continue;
                        }
                    }

                    // Create user
                    $user = User::create([
                        'name' => $rowData['name'],
                        'email' => $rowData['email'],
                        'password' => Hash::make($rowData['password']),
                        'is_active' => true,
                    ]);

                    // Assign kelompok
                    if ($kelompokId) {
                        $user->reviewer_kelompok_id = $kelompokId;
                        $user->saveQuietly();
                    }

                    // Assign roles
                    $user->assignRole(['reviewer', 'panel_reviewer']);

                    $successCount++;
                }

                fclose($handle);

                // Clean up uploaded file
                @unlink($filePath);

                // Build notification
                $bodyLines = [];
                $bodyLines[] = "✅ **{$successCount}** reviewer berhasil ditambahkan.";
                if ($skipCount > 0) {
                    $bodyLines[] = "⏭️ **{$skipCount}** baris dilewati (email sudah terdaftar).";
                }
                if (! empty($errorRows)) {
                    $bodyLines[] = '❌ **'.count($errorRows).'** baris gagal:';
                    // Show max 5 error details
                    foreach (array_slice($errorRows, 0, 5) as $err) {
                        $bodyLines[] = "  • {$err}";
                    }
                    if (count($errorRows) > 5) {
                        $bodyLines[] = '  • ... dan '.(count($errorRows) - 5).' error lainnya.';
                    }
                }

                $status = $successCount > 0 ? 'success' : ($skipCount > 0 ? 'warning' : 'danger');

                Notification::make()
                    ->title('Import Reviewer Selesai')
                    ->body(implode("\n", $bodyLines))
                    ->{$status}()
                    ->persistent()
                    ->send();
            });
    }
}
