<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLocation extends Model
{
    // opening_hours stored as time string (HH:MM) now, so cast to string
    protected $casts = [
        'opening_hours' => 'string',
        'closing_hours' => 'string',
        'is_service_center' => 'boolean',
    ];
    protected $fillable = [
        'name', 'address', 'city', 'phone', 'lat', 'lng', 'google_maps_link',
        'opening_hours', 'closing_hours', 'instagram_link', 'tiktok_link', 'image', 'is_service_center',
    ];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'shop_location_brand');
    }
}
