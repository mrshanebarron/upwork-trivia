<?php

namespace App\Filament\Resources\Stickers;

use App\Filament\Resources\Stickers\Pages\CreateSticker;
use App\Filament\Resources\Stickers\Pages\EditSticker;
use App\Filament\Resources\Stickers\Pages\ListStickers;
use App\Filament\Resources\Stickers\Schemas\StickerForm;
use App\Filament\Resources\Stickers\Tables\StickersTable;
use App\Models\Sticker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StickerResource extends Resource
{
    protected static ?string $model = Sticker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    public static function form(Schema $schema): Schema
    {
        return StickerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StickersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStickers::route('/'),
            'create' => CreateSticker::route('/create'),
            'edit' => EditSticker::route('/{record}/edit'),
        ];
    }
}
