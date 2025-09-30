<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = ['code','type','value','starts_at','ends_at','active'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
