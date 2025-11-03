<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Promo extends Model
{
    protected $fillable = [
        // 'code',
        // 'type',
        // 'value',
        'starts_at',
        'ends_at',
        'active',
        'image',
        'title',
        'slug',
        'content',
        'location',
        // 'has_voucher',
        // 'voucher_count',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
        'has_voucher' => 'boolean',
        'voucher_count' => 'integer',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(PromoVoucher::class);
    }

    protected static function booted(): void
    {
        // Generate slug on create/update
        static::saving(function (Promo $promo): void {
            if ($promo->shouldGenerateSlug()) {
                $promo->slug = $promo->generateUniqueSlug();
            }
        });

        static::created(function (Promo $promo): void {
            if (! $promo->has_voucher || (int) $promo->voucher_count <= 0) {
                return;
            }

            $total = (int) $promo->voucher_count;

            // Generate a set of unique random voucher codes
            $codes = [];
            while (count($codes) < $total) {
                $code = Str::upper(Str::random(10));
                $codes[$code] = $code; // ensure in-memory uniqueness
            }

            // Remove any codes that might already exist in the database (extremely rare)
            do {
                $existing = PromoVoucher::query()
                    ->whereIn('code', array_keys($codes))
                    ->pluck('code')
                    ->all();

                foreach ($existing as $taken) {
                    unset($codes[$taken]);
                }

                // If after removing existing codes we are short, generate more and repeat
                while (count($codes) < $total) {
                    $newCode = Str::upper(Str::random(10));
                    $codes[$newCode] = $newCode;
                }
            } while (! empty($existing) && count($codes) < $total);

            $now = now();
            $rows = [];
            foreach ($codes as $code) {
                $rows[] = [
                    'promo_id' => $promo->id,
                    'code' => $code,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($rows)) {
                PromoVoucher::insert($rows);
            }
        });
    }

    protected function shouldGenerateSlug(): bool
    {
        return empty($this->slug)
            || $this->isDirty(['title', 'starts_at', 'ends_at']);
    }

    protected function generateUniqueSlug(): string
    {
        $base = $this->buildBaseSlug();
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($this->exists, fn ($q) => $q->where('id', '!=', $this->id))
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    protected function buildBaseSlug(): string
    {
        $titlePart = Str::slug((string) $this->title);

        $startPart = optional($this->starts_at)->format('Ymd');
        $endPart = optional($this->ends_at)->format('Ymd');

        $datePart = trim(implode('-', array_filter([$startPart, $endPart])));

        return trim(implode('-', array_filter([$titlePart, $datePart])));
    }
}
