<?php

namespace App\Filament\Resources\Protocols\Pages;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Protocol;
use App\Models\StatusReview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProtocols extends ListRecords
{
    protected static string $resource = ProtocolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['super_admin', 'admin']);

        $statusColumn = 'status_id';
        $exemptedId = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%exempted%'])->value('id') ?? 1;
        $expeditedId = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%expedited%'])->value('id') ?? 3;
        $fullboardId = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%full board%'])->value('id') ?? 2;
        $fastReviewId = StatusReview::whereRaw('LOWER(status_name) LIKE ?', ['%fast review%'])->value('id');

        $userScope = function (Builder $query) use ($user): void {
            $userReviewerKelompokId = $user->reviewer_kelompok_id;

            $query->where(function (Builder $q) use ($user, $userReviewerKelompokId): void {
                $q->where('user_id', $user->id);

                if ($userReviewerKelompokId) {
                    $q->orWhere('reviewer_kelompok_id', $userReviewerKelompokId);
                }
            });
        };

        if (! $isAdmin) {
            $tabs = [
                'all' => Tab::make('All')
                    ->badge(Protocol::query()->where($userScope)->count()),

                'fastReview' => Tab::make('Fast Review')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $fastReviewId)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $fastReviewId)),

                'exempted' => Tab::make('Exempted')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $exemptedId)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)),

                'expedited' => Tab::make('Expedited')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $expeditedId)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)),

                'fullboard' => Tab::make('Full Board')
                    ->badge(Protocol::query()->where($userScope)->where($statusColumn, $fullboardId)->count())
                    ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)),
            ];

            return $tabs;
        }

        return [
            'all' => Tab::make('All')
                ->badge(Protocol::count()),

            'fastReview' => Tab::make('Fast Review')
                ->badge(Protocol::where($statusColumn, $fastReviewId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $fastReviewId)),

            'exempted' => Tab::make('Exempted')
                ->badge(Protocol::where($statusColumn, $exemptedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $exemptedId)),

            'expedited' => Tab::make('Expedited')
                ->badge(Protocol::where($statusColumn, $expeditedId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $expeditedId)),

            'fullboard' => Tab::make('Full Board')
                ->badge(Protocol::where($statusColumn, $fullboardId)->count())
                ->query(fn (Builder $query) => $query->where($statusColumn, $fullboardId)),
        ];
    }
}
