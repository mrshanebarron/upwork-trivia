<?php

namespace App\Filament\Resources\TriviaCodes\Pages;

use App\Filament\Resources\TriviaCodes\TriviaCodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewTriviaCode extends ViewRecord
{
    protected static string $resource = TriviaCodeResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bag Code Information')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Bag Code'),

                        TextEntry::make('title')
                            ->label('Title'),

                        TextEntry::make('description')
                            ->label('Description'),

                        TextEntry::make('is_active')
                            ->label('Active')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),

                        TextEntry::make('qr_url')
                            ->label('Public Landing Page')
                            ->state(fn ($record) => route('trivia.show', ['code' => $record->code]))
                            ->url(fn ($record) => route('trivia.show', ['code' => $record->code]))
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->helperText('Click to preview what users see when they scan the QR code'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
