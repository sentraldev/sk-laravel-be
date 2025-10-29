<?php

namespace App\Filament\Resources\ProductDiscounts;

use App\Filament\Resources\ProductDiscounts\Pages\CreateProductDiscount;
use App\Filament\Resources\ProductDiscounts\Pages\EditProductDiscount;
use App\Filament\Resources\ProductDiscounts\Pages\ListProductDiscounts;
use App\Filament\Resources\ProductDiscounts\Schemas\ProductDiscountForm;
use App\Filament\Resources\ProductDiscounts\Tables\ProductDiscountsTable;
use App\Models\ProductDiscount;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductDiscountResource extends Resource
{
    protected static ?string $model = ProductDiscount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static string|UnitEnum|null $navigationGroup = 'Product';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ProductDiscountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductDiscountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductDiscounts::route('/'),
            'create' => CreateProductDiscount::route('/create'),
            'edit' => EditProductDiscount::route('/{record}/edit'),
        ];
    }
}
