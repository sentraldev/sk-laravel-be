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
                    ->numeric()
                    ->prefix('Rp') // ✅ show Rp in UI
                    ->required()
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $price = (float) ($state ?? 0);
                        $percent = $get('discount_value');
                        $discounted = $get('discounted_price');
                        if ($price > 0 && is_numeric($percent)) {
                            $set('discounted_price', (int) round($price * (1 - ((int) $percent / 100))));
                        } elseif ($price > 0 && is_numeric($discounted)) {
                            $calc = (int) round(100 - (((float) $discounted / $price) * 100));
                            $set('discount_value', max(0, min(100, $calc)));
                        }
                    }),
                TextInput::make('discounted_price')
                    ->label('Discounted Price (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $price = (float) ($get('price') ?? 0);
                        $discounted = (float) ($state ?? 0);
                        if ($price > 0 && $discounted >= 0) {
                            $percent = (int) round(100 - (($discounted / $price) * 100));
                            $set('discount_value', max(0, min(100, $percent)));
                        }
                    }),
                TextInput::make('discount_value')
                    ->label('Discount %')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $price = (float) ($get('price') ?? 0);
                        $percent = (int) ($state ?? 0);
                        if ($price > 0 && $percent >= 0) {
                            $discounted = round($price * (1 - ($percent / 100)));
                            $set('discounted_price', $discounted);
                        }
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
                    ->imageEditor() // optional: adds crop/resize UI
                    ->maxSize(2048) // 2 MB limit
                    ->directory('products') // stored in storage/app/public/brands
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
