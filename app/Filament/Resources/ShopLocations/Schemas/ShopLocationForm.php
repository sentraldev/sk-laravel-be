<?php

namespace App\Filament\Resources\ShopLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MultiSelect;
use Filament\Schemas\Schema;

class ShopLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('address')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('city'),

                TextInput::make('phone')
                    ->tel(),

                TextInput::make('google_maps_link')
                    ->label('Google Maps Link')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Extract lat/lng from URL
                        $matches = [];
                        if (preg_match('/@([0-9\.\-]+),([0-9\.\-]+)/', $state, $matches)) {
                            $set('lat', $matches[1]);
                            $set('lng', $matches[2]);
                        }
                    })
                    ->columnSpanFull(),

                TextInput::make('lat')
                    ->numeric()
                    ->disabled(),

                TextInput::make('lng')
                    ->numeric()
                    ->disabled(),

                TextInput::make('opening_hours'),

                Checkbox::make('is_service_center')
                    ->label('Is Service Center'),

                MultiSelect::make('brands')
                    ->relationship('brands', 'name')
                    ->preload()
                    ->label('Certified Brands')
                    ->columnSpanFull(),
            ]);
    }
}
