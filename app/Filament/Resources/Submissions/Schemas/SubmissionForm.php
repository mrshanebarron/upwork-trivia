<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submission Details')
                    ->description('Question and answer information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('daily_question_id')
                                    ->label('Question')
                                    ->relationship('dailyQuestion', 'question_text')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('The contest question'),

                                Select::make('user_id')
                                    ->label('User')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User who submitted'),

                                Select::make('sticker_id')
                                    ->label('Sticker Location')
                                    ->relationship('sticker', 'location_name')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Where they scanned'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('selected_answer')
                                    ->label('Answer Selected')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'C' => 'C',
                                        'D' => 'D',
                                    ])
                                    ->required()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('User\'s answer choice'),

                                Toggle::make('is_correct')
                                    ->label('Correct Answer')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Whether answer was correct'),

                                DateTimePicker::make('submitted_at')
                                    ->label('Submitted At')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Submission timestamp'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Anti-Cheat Data')
                    ->description('Read-only tracking information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('ip_address')
                                    ->label('IP Address')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Submission IP'),

                                TextInput::make('device_fingerprint')
                                    ->label('Device Fingerprint')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Browser fingerprint'),

                                TextInput::make('random_tiebreaker')
                                    ->label('Tiebreaker')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Random value for tie resolution'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Geolocation')
                    ->description('GPS coordinates')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('GPS latitude'),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('GPS longitude'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
