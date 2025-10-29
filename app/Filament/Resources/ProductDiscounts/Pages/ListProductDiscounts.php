<?php

namespace App\Filament\Resources\ProductDiscounts\Pages;

use App\Filament\Resources\ProductDiscounts\ProductDiscountResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;


class ListProductDiscounts extends ListRecords
{
    protected static string $resource = ProductDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
