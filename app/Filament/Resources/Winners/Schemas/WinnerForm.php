<?php

namespace App\Filament\Resources\Winners\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WinnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('daily_question_id')
                    ->relationship('dailyQuestion', 'id')
                    ->required(),
                Select::make('submission_id')
                    ->relationship('submission', 'id')
                    ->required(),
                TextInput::make('prize_amount')
                    ->required()
                    ->numeric(),
            ]);
    }
}
