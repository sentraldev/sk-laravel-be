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

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    /*
     |--------------------------------------------------------------------------
     | Discount syncing logic
     |--------------------------------------------------------------------------
     | Keep discounted_price and discount_value (1-100) in sync.
     */

    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = $value;

        // Recalculate complementary field if both available
        $price = (float) ($value ?? 0);
        $discounted = isset($this->attributes['discounted_price']) ? (float) $this->attributes['discounted_price'] : null;
        $percent = isset($this->attributes['discount_value']) ? (int) $this->attributes['discount_value'] : null;

        if ($price > 0) {
            if ($percent !== null) {
                $this->attributes['discounted_price'] = round($price * (1 - ($percent / 100)));
            } elseif ($discounted !== null) {
                $calc = (int) round(100 - (($discounted / $price) * 100));
                $this->attributes['discount_value'] = max(0, min(100, $calc));
            }
        }
    }

    public function setDiscountValueAttribute($value): void
    {
        // Clamp to 0-100
        $percent = $value === null ? null : max(0, min(100, (int) $value));
        $this->attributes['discount_value'] = $percent;

        $price = isset($this->attributes['price']) ? (float) $this->attributes['price'] : null;
        if ($price && $percent !== null) {
            $this->attributes['discounted_price'] = round($price * (1 - ($percent / 100)));
        }
    }

    public function setDiscountedPriceAttribute($value): void
    {
        $this->attributes['discounted_price'] = $value;
        $price = isset($this->attributes['price']) ? (float) $this->attributes['price'] : null;
        $discounted = $value !== null ? (float) $value : null;
        if ($price && $discounted !== null && $price > 0) {
            $calc = (int) round(100 - (($discounted / $price) * 100));
            $this->attributes['discount_value'] = max(0, min(100, $calc));
        }
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
