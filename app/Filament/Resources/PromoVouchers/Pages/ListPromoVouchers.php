<?php

namespace App\Filament\Resources\PromoVouchers\Pages;

use App\Filament\Resources\PromoVouchers\PromoVoucherResource;
use Filament\Resources\Pages\ListRecords;

class ListPromoVouchers extends ListRecords
{
    protected static string $resource = PromoVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No Create action; vouchers are generated automatically
        ];
    }
}
