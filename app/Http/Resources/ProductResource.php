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

        // Base payload
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'discount_value' => $this->discount_value,
            'images' => $images,
            'brand' => $this->brand,
            'category' => $this->category,
        ];

        // Replace the 'laptop' section with a dynamic key based on the category name
        // and set its content to the product's details
        $categoryName = data_get($this->category, 'name');
        if ($categoryName) {
            $data[strtolower($categoryName)] = $this->details;
        } else {
            // Fallback if category name is missing
            $data['details'] = $this->details;
        }

        return $data;
    }
}
