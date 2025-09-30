<?php

namespace App\Filament\Resources\ShopLocations\Pages;

use App\Filament\Resources\ShopLocations\ShopLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShopLocations extends ListRecords
{
    protected static string $resource = ShopLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
