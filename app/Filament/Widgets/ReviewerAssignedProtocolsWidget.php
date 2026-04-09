<?php

namespace App\Filament\Widgets;

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
        return Auth::user()->hasRole('reviewer');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Protocol::query()
                    ->whereHas('reviewers', function ($query) {
                        $query->where('users.id', Auth::id())
                              ->where('feedback_status', 'pending');
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
                    ->url(fn (Protocol $record): string => \App\Filament\Resources\Protocols\ProtocolResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
