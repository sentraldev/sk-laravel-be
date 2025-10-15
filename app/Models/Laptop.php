<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'brand',
        'processor',
        'gpu',
        'ram_size',
        'storage_size',
        'specs',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
