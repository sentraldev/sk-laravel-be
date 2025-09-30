<?php

namespace App\Filament\Resources\ShopLocations;

use App\Filament\Resources\ShopLocations\Pages\CreateShopLocation;
use App\Filament\Resources\ShopLocations\Pages\EditShopLocation;
use App\Filament\Resources\ShopLocations\Pages\ListShopLocations;
use App\Filament\Resources\ShopLocations\Schemas\ShopLocationForm;
use App\Filament\Resources\ShopLocations\Tables\ShopLocationsTable;
use App\Models\ShopLocation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShopLocationResource extends Resource
{
    protected static ?string $model = ShopLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static string|UnitEnum|null $navigationGroup = 'Locations';

    public static function form(Schema $schema): Schema
    {
        return ShopLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShopLocationsTable::configure($table);
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
            'index' => ListShopLocations::route('/'),
            'create' => CreateShopLocation::route('/create'),
            'edit' => EditShopLocation::route('/{record}/edit'),
        ];
    }
}
