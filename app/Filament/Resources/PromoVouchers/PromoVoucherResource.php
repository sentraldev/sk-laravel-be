<?php

namespace App\Filament\Resources\PromoVouchers;

use App\Filament\Resources\PromoVouchers\Pages\EditPromoVoucher;
use App\Filament\Resources\PromoVouchers\Pages\ListPromoVouchers;
use App\Filament\Resources\PromoVouchers\Schemas\PromoVoucherForm;
use App\Filament\Resources\PromoVouchers\Tables\PromoVouchersTable;
use App\Models\PromoVoucher;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PromoVoucherResource extends Resource
{
    protected static ?string $model = PromoVoucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;
    protected static string|UnitEnum|null $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return PromoVoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromoVouchersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            // None
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromoVouchers::route('/'),
            // No create page; vouchers are auto-generated
            'edit' => EditPromoVoucher::route('/{record}/edit'),
        ];
    }
}
