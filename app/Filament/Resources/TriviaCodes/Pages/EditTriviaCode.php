<?php

namespace App\Filament\Resources\TriviaCodes\Pages;

use App\Filament\Resources\TriviaCodes\TriviaCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTriviaCode extends EditRecord
{
    protected static string $resource = TriviaCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
