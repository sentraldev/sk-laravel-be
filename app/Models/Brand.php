<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'logo'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shopLocations()
    {
        return $this->belongsToMany(ShopLocation::class, 'shop_location_brand');
    }
}
