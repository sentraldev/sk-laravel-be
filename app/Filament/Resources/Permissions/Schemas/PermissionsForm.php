<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;

class PermissionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label('Permission')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->disabled(),
            Hidden::make('guard_name')
                ->default(config('auth.defaults.guard', 'web')),
            Select::make('roles')
                ->label('Roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
            ]);
    }
}
