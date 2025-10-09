<?php

namespace App\Filament\Resources\Laptops\Pages;

use App\Filament\Resources\Laptops\LaptopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaptop extends EditRecord
{
    protected static string $resource = LaptopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
