<?php

namespace App\Filament\Resources\Stickers\Pages;

use App\Filament\Resources\Stickers\StickerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSticker extends EditRecord
{
    protected static string $resource = StickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
