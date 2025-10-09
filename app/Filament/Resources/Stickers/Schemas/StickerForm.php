<?php

namespace App\Filament\Resources\Stickers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StickerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unique_code')
                    ->required(),
                TextInput::make('location_name'),
                TextInput::make('property_name'),
                TextInput::make('property_manager_id')
                    ->numeric(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                DatePicker::make('installed_at'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('scan_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
