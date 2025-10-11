<?php

namespace App\Filament\Resources\TriviaCodes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TriviaCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bag Code Information')
                    ->description('Unique code printed on poop bags')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Bag Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('4-digit code printed on the bag (e.g., 1234)'),

                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Internal title for this bag code'),

                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->helperText('Optional description or notes about this bag code'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Whether this bag code is active and accessible'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
