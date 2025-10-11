<?php

namespace App\Filament\Resources\DailyQuestions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DailyQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->description('The trivia question that will be displayed to users')
                    ->schema([
                        Textarea::make('question_text')
                            ->label('Question')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('The trivia question text'),

                        Textarea::make('explanation')
                            ->label('Answer Explanation')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Optional explanation shown after the question is answered'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Multiple Choice Options')
                    ->description('Provide 4 answer options (A, B, C, D)')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('option_a')
                                    ->label('Option A')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('option_b')
                                    ->label('Option B')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('option_c')
                                    ->label('Option C')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('option_d')
                                    ->label('Option D')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Select::make('correct_answer')
                            ->label('Correct Answer')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                            ])
                            ->required()
                            ->helperText('Select which option is the correct answer'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Contest Settings')
                    ->description('Prize and scheduling configuration')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('prize_amount')
                                    ->label('Prize Amount ($)')
                                    ->required()
                                    ->numeric()
                                    ->default(10)
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->maxValue(1000)
                                    ->helperText('Dollar amount for the winner'),

                                DateTimePicker::make('scheduled_for')
                                    ->label('Schedule Date/Time')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->helperText('When this question becomes active'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(false)
                                    ->helperText('Whether this question is currently active'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Results & Winner')
                    ->description('Contest results and winner information')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('submission_count')
                                    ->label('Total Submissions')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Total number of answers submitted'),

                                TextInput::make('correct_submission_count')
                                    ->label('Correct Submissions')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Number of correct answers'),

                                Select::make('winner_id')
                                    ->label('Winner')
                                    ->relationship('winnerUser', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('User who won this contest'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
