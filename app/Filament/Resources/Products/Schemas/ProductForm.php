<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;
use App\Models\Category;

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
                    ->required()
                    ->live(),
                Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->relationship('subCategory', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                RichEditor::make('description')
                    ->label('Description')
                    // ->rows(15)
                    ->columnSpanFull(),
                Section::make('Details')
                    ->description('Fields defined by the selected category will appear here and be saved into details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // This placeholder group dynamically builds inputs from Category.fields
                                Group::make()
                                    ->schema(function (Get $get) {
                                        $categoryId = $get('category_id');
                                        if (!$categoryId) return [];
                                        $category = Category::find($categoryId);
                                        if (!$category || empty($category->fields)) return [];

                                        $components = [];
                                        foreach ($category->fields as $field) {
                                            $key = $field['name'] ?? null;
                                            $type = $field['type'] ?? 'string';
                                            if (! $key) continue;

                                            $path = "details.{$key}";
                                            switch ($type) {
                                                case 'integer':
                                                    $components[] = TextInput::make($path)
                                                        ->label(str($key)->headline())
                                                        ->numeric()
                                                        ->live();
                                                    break;
                                                case 'decimal':
                                                    $components[] = TextInput::make($path)
                                                        ->label(str($key)->headline())
                                                        ->numeric()
                                                        ->rule('decimal:0,4')
                                                        ->live();
                                                    break;
                                                case 'boolean':
                                                    $components[] = Toggle::make($path)
                                                        ->label(str($key)->headline());
                                                    break;
                                                case 'text':
                                                    $components[] = Textarea::make($path)
                                                        ->label(str($key)->headline())
                                                        ->rows(5);
                                                    break;
                                                case 'string':
                                                default:
                                                    $components[] = TextInput::make($path)
                                                        ->label(str($key)->headline());
                                                    break;
                                            }
                                        }
                                        return $components;
                                    })
                            ])
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Price (Rp)')
                    ->prefix('Rp') // ✅ show Rp in UI
                    ->required()
                    ->live()
                    ->formatStateUsing(function ($state) {
                        if ($state === null || $state === '') {
                            return $state;
                        }
                        // Price displayed without decimals by default
                        return number_format($state, 0, ',', '.');
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // Convert ID-locale formatted string (1.234.567,89) to numeric for storage
                        if (is_string($state)) {
                            $clean = preg_replace('/[^0-9,\.]/', '', $state);
                            $clean = str_replace('.', '', $clean); // remove thousand separators
                            $clean = str_replace(',', '.', $clean); // convert decimal comma to dot
                            return is_numeric($clean) ? (float) $clean : 0.0;
                        }
                        return is_numeric($state) ? (float) $state : 0.0;
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        // Reformat inside the input using Indonesian separators
                        if (is_string($state)) {
                            $clean = preg_replace('/[^0-9,\.]/', '', $state);
                            $clean = str_replace('.', '', $clean);
                            $clean = str_replace(',', '.', $clean);
                            $value = is_numeric($clean) ? (float) $clean : 0.0;
                        } else {
                            $value = is_numeric($state) ? (float) $state : 0.0;
                        }
                        // For product price, display without decimal places by default
                        $set('price', number_format($value, 0, ',', '.'));
                    })
                    ->rule(function () {
                        return function (string $attribute, $value, $fail) {
                            // Parse possibly localized input for validation
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
                            if ($value === null) {
                                $fail('The price must be a number.');
                                return;
                            }
                            if ($value < 0) {
                                $fail('The price cannot be negative.');
                            }
                        };
                    }),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('images')
                    ->label('Product Photos')
                    ->image() // ✅ restricts to images
                    ->disk('public')
                    ->multiple()
                    ->reorderable()
                    ->maxFiles(5) 
                    ->panelLayout('grid')
                    ->columnSpanFull()
                    ->imageEditor() // optional: adds crop/resize UI
                    ->maxSize(500) // 500 KB per file
                    ->directory('products') // stored in storage/app/public/brands
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
