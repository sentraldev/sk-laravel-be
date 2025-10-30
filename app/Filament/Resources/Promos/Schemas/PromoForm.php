<?php

namespace App\Filament\Resources\Promos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('promos')
                    ->imageEditor()
                    ->columnSpanFull()
                    ->imageEditorViewportWidth(1200)
                    ->imageEditorViewportHeight(560)
                    ->imageCropAspectRatio('1200:560')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('560')
                    ->maxSize(4096),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('location')
                    ->label('Location')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ])
                    ->default('online')
                    ->required(),
                // TextInput::make('slug')
                //     ->label('Slug (ignore)')
                //     ->disabled()
                //     ->dehydrated(false),
                // TextInput::make('code')
                //     ->required(),
                // Select::make('type')
                //     ->options([
                //         'percentage' => 'Percentage',
                //         'fixed' => 'Fixed',
                //     ])
                //     ->required(),
                // TextInput::make('value')
                //     ->required()
                //     ->numeric(),
                RichEditor::make('content')
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                        ['bulletList', 'orderedList'],
                        ['table', 'attachFiles'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                        ['undo', 'redo'],
                    ])
                    ->label('content')
                    ->required()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 24rem; text-align: justify;']),
                DateTimePicker::make('starts_at')
                    ->required()
                    ->default(fn () => now()->setTime(7, 0)),
                DateTimePicker::make('ends_at')
                    ->required()
                    ->default(fn () => now()->setTime(23, 59)),
                Toggle::make('active')
                    ->required(),
                // Toggle::make('has_voucher')
                //     ->label('Generate Vouchers')
                //     ->live(),
                // TextInput::make('voucher_count')
                //     ->numeric()
                //     ->minValue(0)
                //     ->visible(fn ($get) => (bool) $get('has_voucher')),
            ]);
    }
}

