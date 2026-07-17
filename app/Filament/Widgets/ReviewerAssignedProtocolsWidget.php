<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Protocols\ProtocolResource;
use App\Models\Protocol;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ReviewerAssignedProtocolsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'New Protocols Assigned';

    public static function canView(): bool
    {
        // Only visible to reviewers
        return Auth::user()?->hasRole('reviewer') ?? false;
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        if (! $user) {
            return $table->query(Protocol::query()->whereRaw('1=0'));
        }

        return $table
            ->query(
                Protocol::query()
                    ->where(function ($query) use ($user) {
                        $query->whereHas('reviewers', function ($q) use ($user) {
                            $q->where('users.id', $user->id)
                                ->where('feedback_status', 'pending');
                        })
                            ->orWhere(function ($q) use ($user) {
                                if ($user->reviewer_kelompok_id) {
                                    $q->where('reviewer_kelompok_id', $user->reviewer_kelompok_id)
                                        ->whereDoesntHave('reviewers', function ($q2) use ($user) {
                                            $q2->where('users.id', $user->id);
                                        })
                                        ->whereDoesntHave('reviews', function ($q2) use ($user) {
                                            $q2->where('user_id', $user->id);
                                        });
                                } else {
                                    $q->whereRaw('1=0');
                                }
                            });
                    })
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('perihal_pengajuan')
                    ->label('Research Title')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Researcher')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jenis_protocol')
                    ->label('Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Manusia' => 'info',
                        'Hewan' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Submission Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('statusReview.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtoupper($state ?? '')) {
                        'FULL BOARD' => 'danger',
                        'EXEMPTED' => 'success',
                        'EXPEDITED' => 'info',
                        'FAST REVIEW' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Protocol $record): string => ProtocolResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
