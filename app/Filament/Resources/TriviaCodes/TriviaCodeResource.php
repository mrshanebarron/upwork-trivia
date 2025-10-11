<?php

namespace App\Filament\Resources\TriviaCodes;

use App\Filament\Resources\TriviaCodes\Pages\CreateTriviaCode;
use App\Filament\Resources\TriviaCodes\Pages\EditTriviaCode;
use App\Filament\Resources\TriviaCodes\Pages\ListTriviaCodes;
use App\Filament\Resources\TriviaCodes\Pages\ViewTriviaCode;
use App\Filament\Resources\TriviaCodes\Schemas\TriviaCodeForm;
use App\Filament\Resources\TriviaCodes\Tables\TriviaCodesTable;
use App\Models\TriviaCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TriviaCodeResource extends Resource
{
    protected static ?string $model = TriviaCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Bag Codes';

    protected static ?string $modelLabel = 'Bag Code';

    protected static ?string $pluralModelLabel = 'Bag Codes';

    public static function form(Schema $schema): Schema
    {
        return TriviaCodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TriviaCodesTable::configure($table);
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
            'index' => ListTriviaCodes::route('/'),
            'create' => CreateTriviaCode::route('/create'),
            'view' => ViewTriviaCode::route('/{record}'),
            'edit' => EditTriviaCode::route('/{record}/edit'),
        ];
    }
}
