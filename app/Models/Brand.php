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

    public function serviceCenters()
    {
        return $this->hasMany(ServiceCenter::class);
    }
}
