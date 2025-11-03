<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Filament\Resources\Blogs\BlogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate slug from title and timestamp (aligns with created_at timing)
        $timestamp = now()->format('YmdHis');
        $base = ($data['title'] ?? 'post') . '-' . $timestamp;
        $data['slug'] = Str::slug($base);
        return $data;
    }
}
