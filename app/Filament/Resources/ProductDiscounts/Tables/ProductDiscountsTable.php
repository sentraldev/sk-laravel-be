<?php

namespace App\Filament\Resources\ProductDiscounts\Tables;

use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProductDiscountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')->label('Product')->sortable()->searchable(),
                TextColumn::make('product.price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-'),
                TextColumn::make('percentage')
                    ->label('Percentage')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2) . '%' : '-'),
                TextColumn::make('discount_value')
                    ->label('Discount Value')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null ? 'Rp ' . number_format((float) $state, 0, ',', '.') : '-'),
                IconColumn::make('active')->boolean(),
                TextColumn::make('starts_at')->dateTime('Y-m-d H:i'),
                TextColumn::make('ends_at')->dateTime('Y-m-d H:i'),
                TextColumn::make('created_at')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
