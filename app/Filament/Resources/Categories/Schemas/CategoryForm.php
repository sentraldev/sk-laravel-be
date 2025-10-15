<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug'),
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('categories'),
            ]);
    }
}
