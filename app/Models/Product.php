<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'images' => 'array',
        'details' => 'array',
    ];
    protected $fillable = [
        'brand_id','category_id','sub_category_id',
        'sku','name','slug','description','price','discounted_price','discount_value','stock','images','is_active','details'
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function subCategory() { return $this->belongsTo(SubCategory::class); }
    public function promos() { return $this->belongsToMany(Promo::class); }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    // public function laptop()
    // {
    //     return $this->hasOne(Laptop::class);
    // }

    public function discount()
    {
        return $this->hasOne(ProductDiscount::class)
            ->where('active', true);
    }

    /*
     |--------------------------------------------------------------------------
     | Discount syncing logic
     |--------------------------------------------------------------------------
     | Removed: discount fields moved to ProductDiscounts resource.
     */

    public function setPriceAttribute($value): void
    {
        // Only set price; discount handling moved to ProductDiscounts
        $this->attributes['price'] = $value;
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            // If slug not provided, generate from name + sku
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->sku);
            }
        });

        static::updating(function (Product $product) {
            // Regenerate slug if name or sku changed and slug not manually set in this update
            if (!$product->isDirty('slug') && ($product->isDirty('name') || $product->isDirty('sku'))) {
                $product->slug = static::generateUniqueSlug($product->name, $product->sku, $product->id);
            }
        });

        // Optional: mirror selected details keys to columns if present in details
        static::saving(function (Product $product) {
            // Example: if details contains 'discounted_price' or 'price' accidentally
            if (is_array($product->details)) {
                // Place for future mirroring of specific keys to columns
                // e.g., if (isset($product->details['stock'])) $product->stock = (int) $product->details['stock'];
            }
        });
    }

    protected static function generateUniqueSlug(?string $name, ?string $sku, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug(trim(($name ?? 'product') . '-' . ($sku ?? '')));
        $slug = $base;
        $i = 1;
        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
