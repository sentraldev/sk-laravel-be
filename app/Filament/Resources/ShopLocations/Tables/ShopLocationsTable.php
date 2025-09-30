<?php

namespace App\Filament\Resources\ShopLocations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShopLocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)           // show first 30 characters + "…"
                    ->tooltip(fn ($record) => $record->address) // optional: full address on hover
                    ->wrap(false), 
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                // TextColumn::make('lat')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('lng')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
