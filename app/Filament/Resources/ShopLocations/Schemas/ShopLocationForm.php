<?php

namespace App\Filament\Resources\ShopLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShopLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('city'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('lat')
                    ->numeric(),
                TextInput::make('lng')
                    ->numeric(),
                TextInput::make('opening_hours'),
            ]);
    }
}
