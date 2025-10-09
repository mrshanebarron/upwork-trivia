<?php

namespace App\Filament\Resources\Stickers\Pages;

use App\Filament\Resources\Stickers\StickerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStickers extends ListRecords
{
    protected static string $resource = StickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
