<?php

namespace App\Filament\Resources\GiftCards\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GiftCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('winner_id')
                    ->relationship('winner', 'id')
                    ->required(),
                TextInput::make('order_id')
                    ->required(),
                TextInput::make('reward_id')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('redemption_link')
                    ->columnSpanFull(),
                TextInput::make('delivery_method')
                    ->required()
                    ->default('EMAIL'),
                DateTimePicker::make('delivered_at'),
                DateTimePicker::make('redeemed_at'),
                TextInput::make('provider')
                    ->required()
                    ->default('tremendous'),
                Textarea::make('provider_response')
                    ->columnSpanFull(),
                Textarea::make('error_message')
                    ->columnSpanFull(),
            ]);
    }
}
