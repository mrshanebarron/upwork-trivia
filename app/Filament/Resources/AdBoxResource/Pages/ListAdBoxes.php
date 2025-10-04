<?php

namespace App\Filament\Resources\AdBoxResource\Pages;

use App\Filament\Resources\AdBoxResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdBoxes extends ListRecords
{
    protected static string $resource = AdBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
