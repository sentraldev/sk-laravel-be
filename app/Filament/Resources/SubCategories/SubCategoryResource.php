<?php

namespace App\Filament\Resources\SubCategories;

use App\Filament\Resources\SubCategories\Pages\CreateSubCategory;
use App\Filament\Resources\SubCategories\Pages\EditSubCategory;
use App\Filament\Resources\SubCategories\Pages\ListSubCategories;
use App\Filament\Resources\SubCategories\Schemas\SubCategoryForm;
use App\Filament\Resources\SubCategories\Tables\SubCategoriesTable;
use App\Models\SubCategory;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;
    protected static string|UnitEnum|null $navigationGroup = 'Shop Management';

    public static function form(Schema $schema): Schema
    {
        return SubCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubCategories::route('/'),
            'create' => CreateSubCategory::route('/create'),
            'edit' => EditSubCategory::route('/{record}/edit'),
        ];
    }
}
