<?php

namespace App\Filament\Resources\PrizePools\Pages;

use App\Filament\Resources\PrizePools\PrizePoolResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrizePool extends EditRecord
{
    protected static string $resource = PrizePoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
