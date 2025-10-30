<?php

namespace App\Filament\Resources\PromoVouchers\Pages;

use App\Filament\Resources\PromoVouchers\PromoVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromoVoucher extends EditRecord
{
    protected static string $resource = PromoVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
