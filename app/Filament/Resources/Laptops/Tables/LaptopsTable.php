<?php

namespace App\Filament\Resources\Laptops\Tables;

use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class LaptopsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processor')
                    ->label('CPU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gpu')
                    ->label('GPU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ram_size')
                    ->label('RAM (GB)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('storage_size')
                    ->label('Storage (GB)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specs')
                    ->label('Specs')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Product Price')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.discounted_price')
                    ->label('Product Discounted')
                    ->money('idr', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Linked Product')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('promote')
                    ->label('Promote to Product')
                    ->icon('heroicon-o-arrow-up-right')
                    ->visible(fn ($record) => ! $record->product_id)
                    ->form([
                        TextInput::make('sku')->label('SKU')->required(),
                        TextInput::make('price')->label('Price')->numeric()->minValue(0)->required(),
                        TextInput::make('discounted_price')->label('Discounted Price')->numeric()->minValue(0),
                        TextInput::make('stock')->label('Stock')->numeric()->default(0),
                    ])
                    ->action(function ($record, array $data): void {
                        // Resolve models without imports
                        $brandModel = \App\Models\Brand::firstOrCreate(
                            ['name' => $record->brand],
                            ['slug' => \Illuminate\Support\Str::slug($record->brand)]
                        );
                        $categoryModel = \App\Models\Category::firstOrCreate(
                            ['name' => 'Laptop'],
                            ['slug' => \Illuminate\Support\Str::slug('Laptop')]
                        );

                        $price = $data['price'] ?? null;
                        $discounted = $data['discounted_price'] ?? null;
                        if (is_numeric($price) && is_numeric($discounted) && $discounted > $price) {
                            [$price, $discounted] = [$discounted, $price];
                        }

                        $product = \App\Models\Product::firstOrNew(['sku' => $data['sku']]);
                        $product->brand_id = $brandModel->id;
                        $product->category_id = $categoryModel->id;
                        $product->name = $record->name;
                        $product->description = self::buildDescriptionFromLaptop($record);
                        $product->price = $price;
                        $product->discounted_price = $discounted;
                        $product->stock = (int) ($data['stock'] ?? 0);
                        $product->is_active = true;
                        $product->save();

                        // Create a fixed ProductDiscount if discounted < price
                        if (is_numeric($price) && is_numeric($discounted) && $discounted < $price) {
                            \App\Models\ProductDiscount::create([
                                'product_id' => $product->id,
                                'type' => 'fixed',
                                'value' => $price - $discounted,
                                'active' => true,
                            ]);
                        }

                        $record->product_id = $product->id;
                        $record->save();
                    })
                    ->successNotificationTitle('Promoted to Product'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function buildDescriptionFromLaptop($laptop): string
    {
        $parts = [];
        if ($laptop->processor) { $parts[] = 'CPU: ' . $laptop->processor; }
        if ($laptop->gpu) { $parts[] = 'GPU: ' . $laptop->gpu; }
        if ($laptop->ram_size) { $parts[] = 'RAM: ' . $laptop->ram_size . ' GB'; }
        if ($laptop->storage_size) { $parts[] = 'Storage: ' . $laptop->storage_size . ' GB'; }
        return implode("\n", $parts);
    }
}
