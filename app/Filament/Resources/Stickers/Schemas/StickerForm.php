<?php

namespace App\Filament\Resources\Stickers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StickerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sticker Information')
                    ->description('Unique identifier and status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('unique_code')
                                    ->label('Sticker Code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('Unique QR code identifier'),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'damaged' => 'Damaged',
                                        'removed' => 'Removed',
                                    ])
                                    ->required()
                                    ->default('active')
                                    ->helperText('Current sticker status'),

                                DatePicker::make('installed_at')
                                    ->label('Installation Date')
                                    ->native(false)
                                    ->maxDate(now())
                                    ->helperText('When sticker was placed'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Location Details')
                    ->description('Property and geographic information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('location_name')
                                    ->label('Location Name')
                                    ->maxLength(255)
                                    ->helperText('Dog park, trail, etc.'),

                                TextInput::make('property_name')
                                    ->label('Property Name')
                                    ->maxLength(255)
                                    ->helperText('Building or complex name'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('property_manager_id')
                                    ->label('Property Manager ID')
                                    ->numeric()
                                    ->helperText('Rick\'s client ID'),

                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->helperText('GPS latitude'),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->helperText('GPS longitude'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Statistics')
                    ->description('Read-only usage data')
                    ->schema([
                        TextInput::make('scan_count')
                            ->label('Total Scans')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Number of times this sticker was scanned'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
