<?php

namespace App\Filament\Resources\AdBoxResource\Pages;

use App\Filament\Resources\AdBoxResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdBox extends EditRecord
{
    protected static string $resource = AdBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
