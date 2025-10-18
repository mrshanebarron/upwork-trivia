<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabled(fn ($record) => $record !== null) // Prevent editing key on existing records
                    ->helperText('Unique identifier for this setting. Cannot be changed after creation.'),

                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'json' => 'JSON',
                    ])
                    ->required()
                    ->default('string')
                    ->reactive()
                    ->helperText('Data type for proper value casting'),

                Toggle::make('value')
                    ->label('Value')
                    ->visible(fn ($get, $record) => $get('type') === 'boolean' || ($record && $record->type === 'boolean'))
                    ->required(fn ($get, $record) => $get('type') === 'boolean' || ($record && $record->type === 'boolean'))
                    ->helperText('Boolean value (true/false)'),

                RichEditor::make('value')
                    ->label('Value')
                    ->columnSpanFull()
                    ->visible(fn ($get, $record) =>
                        $get('type') !== 'boolean' && (
                            in_array($get('key'), ['about_content', 'terms_content', 'privacy_content']) ||
                            ($record && in_array($record->key, ['about_content', 'terms_content', 'privacy_content']))
                        )
                    )
                    ->required(fn ($get, $record) =>
                        in_array($get('key'), ['about_content', 'terms_content', 'privacy_content']) ||
                        ($record && in_array($record->key, ['about_content', 'terms_content', 'privacy_content']))
                    )
                    ->formatStateUsing(function ($state) {
                        // Handle null or empty values
                        if (empty($state)) {
                            return null;
                        }
                        // If it's already valid HTML/JSON, return as-is
                        return $state;
                    })
                    ->helperText('HTML content for the page. Use the editor to format text.'),

                Textarea::make('value')
                    ->label('Value')
                    ->rows(8)
                    ->columnSpanFull()
                    ->visible(fn ($get, $record) =>
                        $get('type') !== 'boolean' && (
                            in_array($get('key'), ['termly_terms_code', 'termly_privacy_code']) ||
                            ($record && in_array($record->key, ['termly_terms_code', 'termly_privacy_code']))
                        )
                    )
                    ->required(fn ($get, $record) => in_array($get('key'), ['termly_terms_code', 'termly_privacy_code']) || ($record && in_array($record->key, ['termly_terms_code', 'termly_privacy_code'])))
                    ->helperText('Paste the embed code from Termly.io here'),

                Textarea::make('value')
                    ->label('Value')
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn ($get, $record) =>
                        $get('type') !== 'boolean' && (
                            (!in_array($get('key'), ['about_content', 'terms_content', 'privacy_content', 'termly_terms_code', 'termly_privacy_code']) && !$record) ||
                            ($record && !in_array($record->key, ['about_content', 'terms_content', 'privacy_content', 'termly_terms_code', 'termly_privacy_code']) && $record->type !== 'boolean')
                        )
                    )
                    ->required(fn ($get, $record) =>
                        $get('type') !== 'boolean' && (
                            (!in_array($get('key'), ['about_content', 'terms_content', 'privacy_content', 'termly_terms_code', 'termly_privacy_code']) && !$record) ||
                            ($record && !in_array($record->key, ['about_content', 'terms_content', 'privacy_content', 'termly_terms_code', 'termly_privacy_code']) && $record->type !== 'boolean')
                        )
                    )
                    ->helperText('Setting value - will be cast according to the type selected above'),

                TextInput::make('description')
                    ->columnSpanFull()
                    ->helperText('Optional description of what this setting controls'),
            ]);
    }
}
