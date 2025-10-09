<?php

namespace App\Filament\Resources\DailyQuestions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DailyQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('question_text')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('option_a')
                    ->required(),
                TextInput::make('option_b')
                    ->required(),
                TextInput::make('option_c')
                    ->required(),
                TextInput::make('option_d')
                    ->required(),
                TextInput::make('correct_answer')
                    ->required(),
                Textarea::make('explanation')
                    ->columnSpanFull(),
                TextInput::make('prize_amount')
                    ->required()
                    ->numeric()
                    ->default(10),
                DateTimePicker::make('scheduled_for')
                    ->required(),
                Select::make('winner_id')
                    ->relationship('winner', 'name'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('submission_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('correct_submission_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
