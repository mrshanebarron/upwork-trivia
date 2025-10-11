<?php

namespace App\Filament\Resources\Winners\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WinnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Winner Information')
                    ->description('Contest winner details and prize information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('Winner')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User who won the contest'),

                                TextInput::make('prize_amount')
                                    ->label('Prize Amount ($)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->helperText('Dollar amount awarded'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('daily_question_id')
                                    ->label('Question')
                                    ->relationship('dailyQuestion', 'question_text')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('The contest question they won'),

                                Select::make('submission_id')
                                    ->label('Submission')
                                    ->relationship('submission', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('The winning answer submission'),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }
}
