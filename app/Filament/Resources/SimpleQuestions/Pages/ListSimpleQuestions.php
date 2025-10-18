<?php

namespace App\Filament\Resources\SimpleQuestions\Pages;

use App\Filament\Resources\SimpleQuestions\SimpleQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSimpleQuestions extends ListRecords
{
    protected static string $resource = SimpleQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
