<?php

namespace App\Filament\Resources\DailyQuestions\Pages;

use App\Filament\Resources\DailyQuestions\DailyQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyQuestion extends CreateRecord
{
    protected static string $resource = DailyQuestionResource::class;
}
