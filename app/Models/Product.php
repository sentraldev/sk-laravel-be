<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = ['images' => 'array'];
    protected $fillable = [
        'brand_id','category_id','sub_category_id',
        'sku','name','description','price','discounted_price','stock','images','is_active'
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function subCategory() { return $this->belongsTo(SubCategory::class); }
    public function promos() { return $this->belongsToMany(Promo::class); }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function laptop()
    {
        return $this->hasOne(Laptop::class);
    }

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }
}
