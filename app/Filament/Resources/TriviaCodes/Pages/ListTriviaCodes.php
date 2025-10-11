<?php

namespace App\Filament\Resources\TriviaCodes\Pages;

use App\Filament\Resources\TriviaCodes\TriviaCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTriviaCodes extends ListRecords
{
    protected static string $resource = TriviaCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
