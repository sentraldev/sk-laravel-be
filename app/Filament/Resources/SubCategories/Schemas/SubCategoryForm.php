<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug'),
            ]);
    }
}
