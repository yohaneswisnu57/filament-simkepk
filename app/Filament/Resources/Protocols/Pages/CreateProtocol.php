<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\User;
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
        $data = $this->data; // Akses data form yang di-submit

        // Logic Manual Assignment untuk Fast Review
        if (isset($data['fast_review_ketua_id']) && $data['fast_review_ketua_id']) {
            $protocol->reviewers()->attach($data['fast_review_ketua_id'], ['role_in_review' => 'Ketua']);
        }

        if (isset($data['fast_review_secretary_id']) && $data['fast_review_secretary_id']) {
            // Cek duplikasi jika user sama dengan ketua (jarang terjadi tapi mungkin)
            if ($data['fast_review_secretary_id'] != ($data['fast_review_ketua_id'] ?? null)) {
                $protocol->reviewers()->attach($data['fast_review_secretary_id'], ['role_in_review' => 'Sekertaris']);
            }
        }
    }
}
