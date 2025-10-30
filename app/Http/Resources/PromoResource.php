<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PromoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $image = $this->image ? config('app.url') . Storage::url($this->image) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'location' => $this->location,
            'content' => $this->content,
            'image' => $image,
            'starts_at' => optional($this->starts_at)?->toIso8601String(),
            'ends_at' => optional($this->ends_at)?->toIso8601String(),
            'active' => (bool) $this->active,
            // Optional legacy/commercial fields if present
            'code' => $this->code ?? null,
            'type' => $this->type ?? null,
            'value' => $this->value ?? null,
            'has_voucher' => isset($this->has_voucher) ? (bool) $this->has_voucher : null,
            'voucher_count' => $this->voucher_count ?? null,
            'created_at' => optional($this->created_at)?->toIso8601String(),
            'updated_at' => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
