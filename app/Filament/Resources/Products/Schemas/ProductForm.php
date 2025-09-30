<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                Select::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name') // ✅ fetches from brands table
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship('subCategory', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Price (Rp)')
                    ->numeric()
                    ->prefix('Rp') // ✅ show Rp in UI
                    ->required(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('images')
                    ->label('Product Photos')
                    ->image() // ✅ restricts to images
                    ->multiple()
                    ->reorderable()
                    ->maxFiles(5) 
                    ->imageEditor() // optional: adds crop/resize UI
                    ->maxSize(2048) // 2 MB limit
                    ->directory('products') // stored in storage/app/public/brands
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
