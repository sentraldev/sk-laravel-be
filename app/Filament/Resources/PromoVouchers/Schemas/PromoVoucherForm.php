<?php

namespace App\Filament\Resources\PromoVouchers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Promo;

class PromoVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('Voucher Code'),
                Select::make('promo_id')
                    ->relationship('promo', 'title')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('Promo'),
                DateTimePicker::make('redeemed_at')
                    ->label('Redeemed At')
                    ->helperText('Set to a date/time to mark this voucher as redeemed.'),
            ]);
    }
}
