<?php

namespace App\Filament\Resources\ServiceCenters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceCenterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('brand_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('lat')
                    ->numeric(),
                TextInput::make('lng')
                    ->numeric(),
            ]);
    }
}
