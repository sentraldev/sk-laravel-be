<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $fullUrl = config('app.url') . Storage::url($this->image);

        return [
            'id' => $this->id,
            'title'=> $this->title,
            'image'=> $fullUrl,
        ];
    }
}
