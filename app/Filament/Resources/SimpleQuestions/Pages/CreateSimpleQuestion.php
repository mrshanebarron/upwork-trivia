<?php

namespace App\Filament\Resources\SimpleQuestions\Pages;

use App\Filament\Resources\SimpleQuestions\SimpleQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSimpleQuestion extends CreateRecord
{
    protected static string $resource = SimpleQuestionResource::class;
}
