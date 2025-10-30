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
                    ->imageEditorViewportWidth(1200)
                    ->imageEditorViewportHeight(560)
                    ->imageCropAspectRatio('1200:560')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('560')
                    ->imageResizeMode('cover')
                    ->imageResizeUpscale(false)
                    ->maxSize(500)
                    ->columnSpanFull()
                    ->visibility('public')
                    ->required()
                    ->rules(['dimensions:width=1200,height=560'])
                    ->helperText('Image must be exactly 1200x560px'),
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
