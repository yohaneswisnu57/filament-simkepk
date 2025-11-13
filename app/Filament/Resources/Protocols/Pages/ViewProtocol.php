<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProtocol extends ViewRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        // return [
        //     EditAction::make(),
        // ];

        // 👇 3. Tambahkan seluruh metode ini
        return [
            EditAction::make(), // Tombol Edit (jika ada)

            // Tombol untuk menambah review
            Action::make('addReview')
                ->label('Beri Review/Komentar')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('info')

                // Hanya tampilkan jika user adalah 'reviewer' (sesuaikan nama role)
                ->visible(fn () => auth()->user()->hasRole(['reviewer', 'admin', 'super_admin', 'sekertaris']))

                // Ini akan memunculkan form modal
                ->form([
                    Textarea::make('comment')
                        ->label('Komentar Review')
                        ->required()
                        ->minLength(5),
                ])

                // Logika saat tombol "Submit" di modal ditekan
                ->action(function (array $data) {
                    // $this->record adalah data Protocol yang sedang dibuka
                    $this->record->reviews()->create([
                        'comment' => $data['comment'],
                        'user_id' => auth()->id(),
                    ]);

                    // Kirim notifikasi sukses
                    Notification::make()
                        ->title('Review berhasil disimpan')
                        ->success()
                        ->send();
                })
        ];

    }
}
