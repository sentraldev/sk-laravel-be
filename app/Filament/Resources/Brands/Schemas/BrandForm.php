<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug'),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->image() // ✅ restricts to images
                    ->imageEditor() // optional: adds crop/resize UI
                    ->maxSize(2048) // 2 MB limit
                    ->directory('brands'), // stored in storage/app/public/brands
            ]);
    }
}
