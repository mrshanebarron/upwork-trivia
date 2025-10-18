<?php

namespace App\Filament\Resources\SimpleQuestions;

use App\Filament\Resources\SimpleQuestions\Pages\CreateSimpleQuestion;
use App\Filament\Resources\SimpleQuestions\Pages\EditSimpleQuestion;
use App\Filament\Resources\SimpleQuestions\Pages\ListSimpleQuestions;
use App\Filament\Resources\SimpleQuestions\Pages\ViewSimpleQuestion;
use App\Filament\Resources\SimpleQuestions\Schemas\SimpleQuestionForm;
use App\Filament\Resources\SimpleQuestions\Schemas\SimpleQuestionInfolist;
use App\Filament\Resources\SimpleQuestions\Tables\SimpleQuestionsTable;
use App\Models\SimpleQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SimpleQuestionResource extends Resource
{
    protected static ?string $model = SimpleQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SimpleQuestionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SimpleQuestionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SimpleQuestionsTable::configure($table);
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
            'index' => ListSimpleQuestions::route('/'),
            'create' => CreateSimpleQuestion::route('/create'),
            'view' => ViewSimpleQuestion::route('/{record}'),
            'edit' => EditSimpleQuestion::route('/{record}/edit'),
        ];
    }
}
