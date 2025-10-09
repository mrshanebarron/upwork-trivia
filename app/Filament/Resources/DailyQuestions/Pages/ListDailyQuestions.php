<?php

namespace App\Filament\Resources\DailyQuestions\Pages;

use App\Filament\Resources\DailyQuestions\DailyQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyQuestions extends ListRecords
{
    protected static string $resource = DailyQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
