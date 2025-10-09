<?php

namespace App\Filament\Resources\DailyQuestions;

use App\Filament\Resources\DailyQuestions\Pages\CreateDailyQuestion;
use App\Filament\Resources\DailyQuestions\Pages\EditDailyQuestion;
use App\Filament\Resources\DailyQuestions\Pages\ListDailyQuestions;
use App\Filament\Resources\DailyQuestions\Schemas\DailyQuestionForm;
use App\Filament\Resources\DailyQuestions\Tables\DailyQuestionsTable;
use App\Models\DailyQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyQuestionResource extends Resource
{
    protected static ?string $model = DailyQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DailyQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyQuestionsTable::configure($table);
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
            'index' => ListDailyQuestions::route('/'),
            'create' => CreateDailyQuestion::route('/create'),
            'edit' => EditDailyQuestion::route('/{record}/edit'),
        ];
    }
}
