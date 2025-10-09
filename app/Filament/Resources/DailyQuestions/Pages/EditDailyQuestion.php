<?php

namespace App\Filament\Resources\DailyQuestions\Pages;

use App\Filament\Resources\DailyQuestions\DailyQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyQuestion extends EditRecord
{
    protected static string $resource = DailyQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
