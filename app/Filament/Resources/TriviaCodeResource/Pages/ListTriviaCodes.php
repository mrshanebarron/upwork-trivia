<?php

namespace App\Filament\Resources\TriviaCodeResource\Pages;

use App\Filament\Resources\TriviaCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTriviaCodes extends ListRecords
{
    protected static string $resource = TriviaCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
