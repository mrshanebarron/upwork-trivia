<?php

namespace App\Filament\Resources\SimpleQuestions\Pages;

use App\Filament\Resources\SimpleQuestions\SimpleQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSimpleQuestion extends EditRecord
{
    protected static string $resource = SimpleQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
