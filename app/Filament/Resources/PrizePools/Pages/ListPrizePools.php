<?php

namespace App\Filament\Resources\PrizePools\Pages;

use App\Filament\Resources\PrizePools\PrizePoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrizePools extends ListRecords
{
    protected static string $resource = PrizePoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
