<?php

namespace App\Filament\Resources\SimpleQuestions\Pages;

use App\Filament\Resources\SimpleQuestions\SimpleQuestionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSimpleQuestion extends ViewRecord
{
    protected static string $resource = SimpleQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
