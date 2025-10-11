<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->description('Basic user account details')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('User\'s full name'),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Must be unique'),
                            ]),

                        Grid::make(1)
                            ->schema([
                                DatePicker::make('birthdate')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->maxDate(now())
                                    ->helperText('Required for age verification'),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('When the user verified their email'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Password')
                    ->description('Set or update the user password')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn ($record) => $record === null)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable()
                            ->helperText('Leave blank to keep current password (on edit)'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Permissions & Settings')
                    ->description('User role and privacy settings')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Toggle::make('is_admin')
                                    ->label('Administrator')
                                    ->default(false)
                                    ->helperText('Grants admin panel access'),

                                Toggle::make('show_name_publicly')
                                    ->label('Show Name Publicly')
                                    ->default(true)
                                    ->helperText('Display name on public winner lists'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Contest Statistics')
                    ->description('Read-only contest participation data')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('total_winnings')
                                    ->label('Total Winnings ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Lifetime winnings from all contests'),

                                DateTimePicker::make('last_won_at')
                                    ->label('Last Win Date')
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Date of most recent contest win'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
