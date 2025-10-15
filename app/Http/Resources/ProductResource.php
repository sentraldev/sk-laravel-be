<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $images = $this->images;
        if (is_array($images)) {
            $images = array_values(array_filter(array_map(function ($path) {
                if (!$path) return null;
                return config('app.url') . Storage::url($path);
            }, $images)));
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand?->name,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'images' => $images,
            'laptop' => $this->laptop ? [
                'processor' => $this->laptop->processor,
                'gpu' => $this->laptop->gpu,
                'ram' => $this->laptop->ram_size,
                'storage' => $this->laptop->storage_size,
                'specs' => $this->laptop->specs,
            ] : null,
        ];
    }
}
