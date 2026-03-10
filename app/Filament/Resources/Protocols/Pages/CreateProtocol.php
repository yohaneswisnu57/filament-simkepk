<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProtocol extends CreateRecord
{
    protected static string $resource = ProtocolResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $protocol = $this->record;
        $data = $this->data;

        $ketuaId = $data['fast_review_ketua_id'] ?? null;
        $sekertarisId = $data['fast_review_secretary_id'] ?? null;

        if (! $ketuaId && ! $sekertarisId) {
            return;
        }

        if ($ketuaId) {
            $protocol->reviewers()->attach($ketuaId, [
                'role_in_review' => 'Ketua',
                'feedback_status' => 'pending',
            ]);
        }

        if ($sekertarisId && $sekertarisId != $ketuaId) {
            $protocol->reviewers()->attach($sekertarisId, [
                'role_in_review' => 'Sekertaris',
                'feedback_status' => 'pending',
            ]);
        }

        $protocol->update(['fast_review_decision' => 'Pending']);
    }
}
