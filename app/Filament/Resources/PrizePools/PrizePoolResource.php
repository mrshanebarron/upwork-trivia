<?php

namespace App\Filament\Resources\PrizePools;

use App\Filament\Resources\PrizePools\Pages\CreatePrizePool;
use App\Filament\Resources\PrizePools\Pages\EditPrizePool;
use App\Filament\Resources\PrizePools\Pages\ListPrizePools;
use App\Filament\Resources\PrizePools\Schemas\PrizePoolForm;
use App\Filament\Resources\PrizePools\Tables\PrizePoolsTable;
use App\Models\PrizePool;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrizePoolResource extends Resource
{
    protected static ?string $model = PrizePool::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PrizePoolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrizePoolsTable::configure($table);
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
            'index' => ListPrizePools::route('/'),
            'create' => CreatePrizePool::route('/create'),
            'edit' => EditPrizePool::route('/{record}/edit'),
        ];
    }
}
