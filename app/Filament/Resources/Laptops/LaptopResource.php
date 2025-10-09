<?php

namespace App\Filament\Resources\Laptops;

use App\Filament\Resources\Laptops\Pages\CreateLaptop;
use App\Filament\Resources\Laptops\Pages\EditLaptop;
use App\Filament\Resources\Laptops\Pages\ListLaptops;
use App\Filament\Resources\Laptops\Schemas\LaptopForm;
use App\Filament\Resources\Laptops\Tables\LaptopsTable;
use App\Models\Laptop;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LaptopResource extends Resource
{
    protected static ?string $model = Laptop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;
    protected static string|UnitEnum|null $navigationGroup = 'Product';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LaptopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaptopsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaptops::route('/'),
            'create' => CreateLaptop::route('/create'),
            'edit' => EditLaptop::route('/{record}/edit'),
        ];
    }
}
