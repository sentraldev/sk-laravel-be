<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select as FormsSelect;
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
                    ->maxSize(1024)
                    ->directory('categories'),
                Repeater::make('fields')
                    ->label('Detail')
                    ->schema([
                        TextInput::make('name')
                            ->label('Component')
                            ->required()
                            ->helperText('Use snake_case keys like ram_size, storage_size'),
                        FormsSelect::make('type')
                            ->label('Data Type')
                            ->options([
                                'string' => 'Text',
                                'integer' => 'Round Number',
                                'decimal' => 'Decimal Number',
                                'boolean' => 'Boolean (True / False)',
                                'text' => 'Long Text',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    // ->collapsed()
                    ->grid(3)
                    ->addActionLabel('Add Field')
                    ->reorderable()
                    ->default([])
                    ->columnSpanFull(),
            ]);
    }
}
