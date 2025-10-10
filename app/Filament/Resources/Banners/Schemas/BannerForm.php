<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('banners')
                    ->imagePreviewHeight(300)
                    ->maxSize(4096)
                    ->columnSpanFull()
                    ->visibility('public')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                TextInput::make('link_url')
                    ->label('Link URL')
                    ->url()
                    ->nullable(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
