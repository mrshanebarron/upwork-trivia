<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecentWinnersTable extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Winner::query()
                    ->with(['user', 'dailyQuestion', 'giftCard'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Winner')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('dailyQuestion.question_text')
                    ->label('Question')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->dailyQuestion->question_text),
                \Filament\Tables\Columns\TextColumn::make('prize_amount')
                    ->label('Prize')
                    ->money('USD'),
                \Filament\Tables\Columns\BadgeColumn::make('giftCard.status')
                    ->label('Gift Card')
                    ->colors([
                        'success' => 'delivered',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Won At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
