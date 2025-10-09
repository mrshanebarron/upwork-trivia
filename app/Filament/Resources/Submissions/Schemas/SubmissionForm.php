<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('daily_question_id')
                    ->relationship('dailyQuestion', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('selected_answer')
                    ->required(),
                Toggle::make('is_correct')
                    ->required(),
                TextInput::make('ip_address'),
                TextInput::make('device_fingerprint'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Select::make('sticker_id')
                    ->relationship('sticker', 'id'),
                DateTimePicker::make('submitted_at')
                    ->required(),
                TextInput::make('random_tiebreaker')
                    ->required()
                    ->numeric(),
            ]);
    }
}
