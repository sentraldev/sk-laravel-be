<?php

namespace App\Filament\Resources\Promos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
