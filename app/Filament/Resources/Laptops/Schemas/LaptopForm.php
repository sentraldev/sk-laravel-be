<?php

namespace App\Filament\Resources\Laptops\Schemas;

use App\Models\Brand;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LaptopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->columnSpanFull(),
            Select::make('brand')
                ->label('Brand')
                ->options(fn (): array => Brand::query()->orderBy('name')->pluck('name', 'name')->all())
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('processor')->label('Processor'),
            TextInput::make('gpu')->label('GPU'),
            TextInput::make('ram_size')->numeric()->minValue(0)->suffix(' GB')->label('RAM Size'),
            TextInput::make('storage_size')->numeric()->minValue(0)->suffix(' GB')->label('Storage Size'),
            Textarea::make('specs')->label('Specs')->rows(15)->columnSpanFull(),

            // optional relation to existing product
            Select::make('product_id')
                ->label('Linked Product (optional)')
                ->relationship('product', 'name')
                ->searchable()
                ->preload(),
        ]);
    }
}
