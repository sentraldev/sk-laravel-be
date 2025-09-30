<?php

namespace App\Filament\Resources\ProductReviews\Pages;

use App\Filament\Resources\ProductReviews\ProductReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductReview extends EditRecord
{
    protected static string $resource = ProductReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
