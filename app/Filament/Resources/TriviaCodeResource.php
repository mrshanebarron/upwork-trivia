<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TriviaCodeResource\Pages;
use App\Filament\Resources\TriviaCodeResource\RelationManagers;
use App\Models\TriviaCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TriviaCodeResource extends Resource
{
    protected static ?string $model = TriviaCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(4)
                    ->label('4-Digit Code'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Forms\Components\Repeater::make('answers')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->required()
                            ->default(fn ($get) => 1),
                        Forms\Components\Textarea::make('answer')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->orderColumn('order')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('answers_count')
                    ->counts('answers')
                    ->label('Answers')
                    ->badge(),
                Tables\Columns\TextColumn::make('views_count')
                    ->counts('views')
                    ->label('Views')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTriviaCodes::route('/'),
            'create' => Pages\CreateTriviaCode::route('/create'),
            'edit' => Pages\EditTriviaCode::route('/{record}/edit'),
        ];
    }
}
