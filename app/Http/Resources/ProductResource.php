<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand?->name,
            'price' => $this->price,
            'discounted_price' => $this->discounted_price,
            'laptop' => $this->laptop ? [
                'processor' => $this->laptop->processor,
                'gpu' => $this->laptop->gpu,
                'ram' => $this->laptop->ram,
                'storage' => $this->laptop->storage,
                'specs' => $this->laptop->specs,
            ] : null,
        ];
    }
}
