<?php

namespace App\Filament\Resources\ShopLocations\Pages;

use App\Filament\Resources\ShopLocations\ShopLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShopLocation extends EditRecord
{
    protected static string $resource = ShopLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
