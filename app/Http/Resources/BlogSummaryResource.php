<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @mixin \App\Models\Blog */
class BlogSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = null;
        if (!empty($this->image)) {
            $imageUrl = Storage::disk('public')->url($this->image);
        }

        $excerpt = Str::limit(trim(strip_tags($this->content ?? '')), 160);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $excerpt,
            'image_url' => $imageUrl,
            'author' => optional($this->creator)->name,
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}
