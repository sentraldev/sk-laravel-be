<?php

namespace App\Filament\Resources\Promos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->label('Image')
                    ->circular()
                    ->size(40),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('location')
                    ->badge()
                    ->sortable(),
                // TextColumn::make('slug')
                //     ->label('Slug')
                //     ->searchable()
                //     ->limit(40),
                // TextColumn::make('code')
                //     ->searchable(),
                // TextColumn::make('type')
                //     ->searchable(),
                // TextColumn::make('value')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('active')
                    ->boolean(),
                // IconColumn::make('has_voucher')
                //     ->boolean()
                //     ->label('Has Vouchers'),
                // TextColumn::make('voucher_count')
                //     ->numeric()
                //     ->label('Voucher Count')
                //     ->toggleable(isToggledHiddenByDefault: true),
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
