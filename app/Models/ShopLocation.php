<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLocation extends Model
{
    protected $casts = ['opening_hours' => 'array'];
    protected $fillable = ['name','address','city','phone','lat','lng','opening_hours'];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'shop_location_brand');
    }
}
