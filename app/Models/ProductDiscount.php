<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    protected $fillable = [
        'product_id', 'percentage', 'discount_value', 'starts_at', 'ends_at', 'active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
