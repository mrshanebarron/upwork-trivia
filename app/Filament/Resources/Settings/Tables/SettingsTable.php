<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereIn('key', [
                'about_content',
                'termly_terms_code',
                'termly_privacy_code',
            ]))
            ->columns([
                TextColumn::make('key')
                    ->label('Page')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'about_content' => 'About Page',
                        'termly_terms_code' => 'Terms of Service',
                        'termly_privacy_code' => 'Privacy Policy',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // No bulk actions - prevent accidental deletion of page content
            ]);
    }
}
