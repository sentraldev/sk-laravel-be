<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoVoucher extends Model
{
    protected $fillable = [
        'promo_id',
        'code',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }
}
