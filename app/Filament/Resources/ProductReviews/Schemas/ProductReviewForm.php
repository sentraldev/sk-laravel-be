<?php

namespace App\Filament\Resources\ProductReviews\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ProductReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('author_name')
                ->label('Reviewer Name')
                ->required(),

            Select::make('rating')
                ->label('Rating')
                ->options([
                    1 => '⭐',
                    2 => '⭐⭐',
                    3 => '⭐⭐⭐',
                    4 => '⭐⭐⭐⭐',
                    5 => '⭐⭐⭐⭐⭐',
                ])
                ->required(),

            Textarea::make('comment')
                ->label('Review Comment')
                ->columnSpanFull(),
            ]);
    }
}
