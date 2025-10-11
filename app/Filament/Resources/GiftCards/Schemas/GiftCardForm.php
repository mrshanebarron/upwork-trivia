<?php

namespace App\Filament\Resources\GiftCards\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GiftCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Recipient Information')
                    ->description('User and winner details for this gift card')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label('User')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('User receiving the gift card'),

                                Select::make('winner_id')
                                    ->label('Winner Record')
                                    ->relationship('winner', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Associated winner record'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Gift Card Details')
                    ->description('Provider and reward information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('provider')
                                    ->label('Provider')
                                    ->options([
                                        'tremendous' => 'Tremendous',
                                        'tango' => 'Tango Card',
                                        'other' => 'Other',
                                    ])
                                    ->required()
                                    ->default('tremendous')
                                    ->helperText('Gift card provider'),

                                TextInput::make('order_id')
                                    ->label('Order ID')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Provider order identifier'),

                                TextInput::make('reward_id')
                                    ->label('Reward ID')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Provider reward identifier'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->helperText('Gift card value'),

                                Select::make('currency')
                                    ->label('Currency')
                                    ->options([
                                        'USD' => 'USD',
                                        'CAD' => 'CAD',
                                        'EUR' => 'EUR',
                                    ])
                                    ->required()
                                    ->default('USD')
                                    ->helperText('Currency code'),

                                Select::make('delivery_method')
                                    ->label('Delivery Method')
                                    ->options([
                                        'EMAIL' => 'Email',
                                        'SMS' => 'SMS',
                                        'LINK' => 'Link Only',
                                    ])
                                    ->required()
                                    ->default('EMAIL')
                                    ->helperText('How card is delivered'),
                            ]),

                        Textarea::make('redemption_link')
                            ->label('Redemption Link')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('URL where user can redeem the card'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status & Tracking')
                    ->description('Delivery status and timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'delivered' => 'Delivered',
                                        'redeemed' => 'Redeemed',
                                        'failed' => 'Failed',
                                    ])
                                    ->required()
                                    ->default('pending')
                                    ->helperText('Current delivery status'),

                                DateTimePicker::make('delivered_at')
                                    ->label('Delivered At')
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('When card was delivered'),

                                DateTimePicker::make('redeemed_at')
                                    ->label('Redeemed At')
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('When card was redeemed'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Provider Response & Errors')
                    ->description('Read-only provider data')
                    ->schema([
                        Textarea::make('provider_response')
                            ->label('Provider Response')
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Raw response from provider API'),

                        Textarea::make('error_message')
                            ->label('Error Message')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Error details if delivery failed'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
