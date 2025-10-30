<?php

namespace App\Filament\Resources\Promos\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Actions\EditAction;

class VouchersRelationManager extends RelationManager
{
    protected static string $relationship = 'vouchers';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                IconColumn::make('is_redeemed')
                    ->boolean()
                    ->label('Redeemed')
                    ->getStateUsing(fn ($record) => ! is_null($record->redeemed_at)),
                TextColumn::make('redeemed_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
