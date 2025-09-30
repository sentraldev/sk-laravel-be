<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand_id')
                    ->required()
                    ->numeric(),
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sub_category_id')
                    ->numeric(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('images'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
