<?php

namespace App\Filament\Resources\ProductDiscounts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Utilities\Get as SchemaGet;
use Filament\Schemas\Components\Utilities\Set as SchemaSet;
use App\Models\Product;
use Closure;

class ProductDiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function (SchemaSet $set, SchemaGet $get, $state) {
                    if (!$state) {
                        return;
                    }
                    $price = Product::query()->whereKey($state)->value('price');
                    if ($price === null || (float) $price <= 0) {
                        $set('percentage', 0);
                        $set('discount_value', '0,00');
                        return;
                    }

                    $currentPct = $get('percentage');
                    $currentVal = $get('discount_value');

                    if (is_numeric($currentPct) && (float) $currentPct > 0) {
                        // Recalculate discount value from existing percentage
                        $discount = round(((float) $price) * (((float) $currentPct) / 100), 2);
                        $set('discount_value', number_format($discount, 2, ',', '.'));
                    } elseif (is_numeric($currentVal) && (float) $currentVal > 0) {
                        // Recalculate percentage from existing discount value
                        $pct = round((((float) $currentVal) / (float) $price) * 100, 2);
                        $set('percentage', $pct);
                    } else {
                        $set('percentage', 0);
                        $set('discount_value', '0,00');
                    }
                }),
            Placeholder::make('product_price')
                ->label('Product Price')
                ->reactive()
                ->content(function (SchemaGet $get) {
                    $productId = $get('product_id');
                    if (!$productId) {
                        return '-';
                    }
                    $price = Product::query()->whereKey($productId)->value('price');
                    if ($price === null) {
                        return '-';
                    }
                    return 'Rp ' . number_format((float) $price, 0, ',', '.');
                }),
            TextInput::make('percentage')
                ->label('Percentage (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->step('0.01')
                ->live()
                ->afterStateUpdated(function (SchemaSet $set, SchemaGet $get, $state) {
                    $productId = $get('product_id');
                    if (!$productId) {
                        return;
                    }
                    $price = Product::query()->whereKey($productId)->value('price');
                    if ($price === null) {
                        return;
                    }
                    $pct = is_numeric($state) ? (float) $state : 0.0;
                    $discount = round(((float) $price) * ($pct / 100), 2);
                    // Update discount_value field with Indonesian formatting
                    $set('discount_value', number_format($discount, 2, ',', '.'));
                }),
            TextInput::make('discount_value')
                ->label('Discount Value')
                ->prefix('Rp')
                ->minValue(0)
                ->step('0.01')
                ->live()
                ->formatStateUsing(function ($state) {
                    if ($state === null || $state === '') {
                        return $state;
                    }
                    // If already a formatted string, try to parse then reformat
                    // if (is_string($state)) {
                    //     $clean = preg_replace('/[^0-9,\.]/', '', $state);
                    //     $clean = str_replace('.', '', $clean);
                    //     $clean = str_replace(',', '.', $clean);
                    //     $value = is_numeric($clean) ? (float) $clean : null;
                    // } else {
                    //     $value = is_numeric($state) ? (float) $state : null;
                    // }
                    // if ($value === null) return $state;
                    return number_format($state, 2, ',', '.');
                })
                ->dehydrateStateUsing(function ($state) {
                    // Convert ID-locale formatted string to a float for storage
                    if (is_string($state)) {
                        $clean = preg_replace('/[^0-9,\.]/', '', $state);
                        $clean = str_replace('.', '', $clean); // remove thousand separators
                        $clean = str_replace(',', '.', $clean); // convert decimal comma to dot
                        return is_numeric($clean) ? round((float) $clean, 2) : 0.0;
                    }
                    return is_numeric($state) ? round((float) $state, 2) : 0.0;
                })
                ->afterStateUpdated(function (SchemaSet $set, SchemaGet $get, $state) {
                    $productId = $get('product_id');
                    if (!$productId) {
                        return;
                    }
                    $price = Product::query()->whereKey($productId)->value('price');
                    if ($price === null || (float) $price <= 0) {
                        $set('percentage', 0);
                        return;
                    }
                    // Parse Indonesian-locale input (e.g., 1.234,56)
                    if (is_string($state)) {
                        $clean = preg_replace('/[^0-9,\.]/', '', $state);
                        $clean = str_replace('.', '', $clean);
                        $clean = str_replace(',', '.', $clean);
                        $value = is_numeric($clean) ? (float) $clean : 0.0;
                    } else {
                        $value = is_numeric($state) ? (float) $state : 0.0;
                    }
                    $pct = round(($value / (float) $price) * 100, 2);
                    $set('percentage', $pct);
                    // Reformat field to Indonesian locale inside the input
                    $set('discount_value', number_format($value, 2, ',', '.'));
                })
                ->rule(function (SchemaGet $get) {
                    return function (string $attribute, $value, Closure $fail) use ($get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return;
                        }
                        // Parse possibly-masked value
                        if (is_string($value)) {
                            $clean = preg_replace('/[^0-9,\.]/', '', $value);
                            $clean = str_replace('.', '', $clean);
                            $clean = str_replace(',', '.', $clean);
                            $value = is_numeric($clean) ? (float) $clean : null;
                        } elseif (is_numeric($value)) {
                            $value = (float) $value;
                        } else {
                            $value = null;
                        }
                        if ($value === null) return;
                        $price = Product::query()->whereKey($productId)->value('price');
                        if ($price !== null && (float) $value > (float) $price) {
                            $fail('Discount value cannot exceed product price.');
                        }
                    };
                }),
            DateTimePicker::make('starts_at')
                ->label('Starts At')
                ->seconds(false),
            DateTimePicker::make('ends_at')
                ->label('Ends At')
                ->seconds(false),
            Toggle::make('active')
                ->label('Active')
                ->default(true),
        ]);
    }
}
