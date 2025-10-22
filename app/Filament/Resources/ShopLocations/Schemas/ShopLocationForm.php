<?php

namespace App\Filament\Resources\ShopLocations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\FileUpload;
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

                FileUpload::make('image')
                    ->label('Shop Image')
                    ->disk('public')
                    ->directory('shop_locations')
                    ->image()
                    ->columnSpanFull(),

                TextInput::make('address')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('city')->required(),

                TextInput::make('phone')
                    ->required()
                    ->tel(),

                TextInput::make('google_maps_link')
                    ->label('Google Maps Link')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Extract lat/lng from URL
                        if (! is_string($state) || $state === '') {
                            return;
                        }

                        // Pattern 1: .../@lat,lng...
                        $m = [];
                        if (preg_match('/@([\-0-9\.]+),([\-0-9\.]+)/', $state, $m, PREG_OFFSET_CAPTURE)) {
                            $lat = $m[1][0];
                            $lng = $m[2][0];
                            $endPos = $m[2][1] + strlen($lng); // end of lng
                            $trimmed = substr($state, 0, $endPos);
                            $set('lat', $lat);
                            $set('lng', $lng);
                            if ($trimmed !== $state) {
                                $set('google_maps_link', $trimmed);
                            }
                            return;
                        }

                        // Pattern 2: ...?q=lat,lng or &q=lat,lng
                        if (preg_match('/[?&]q=([\-0-9\.]+),([\-0-9\.]+)/', $state, $m, PREG_OFFSET_CAPTURE)) {
                            $lat = $m[1][0];
                            $lng = $m[2][0];
                            $endPos = $m[2][1] + strlen($lng);
                            $trimmed = substr($state, 0, $endPos);
                            $set('lat', $lat);
                            $set('lng', $lng);
                            if ($trimmed !== $state) {
                                $set('google_maps_link', $trimmed);
                            }
                        }
                    })
                    ->columnSpanFull(),

                TextInput::make('lat')
                    ->label('Latitude (auto from link)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('lng')
                    ->label('Longitude (auto from link)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),

                // Use a time selector for opening hours (hour:minute, no seconds)
                TimePicker::make('opening_hours')
                    ->required()
                    ->seconds(false),

                // Closing hour (HH:MM, no seconds)
                TimePicker::make('closing_hours')
                    ->required()
                    ->seconds(false),

                TextInput::make('instagram_link')
                    ->label('Instagram Link')
                    ->url()
                    ->columnSpanFull(),

                TextInput::make('tiktok_link')
                    ->label('TikTok Link')
                    ->url()
                    ->columnSpanFull(),

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
