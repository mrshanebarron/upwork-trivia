<?php

namespace App\Filament\Resources\PrizePools\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrizePoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('month')
                    ->required(),
                TextInput::make('budget')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('spent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sponsor_id')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
