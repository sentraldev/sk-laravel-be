<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'blog_category_id',
        'content',
        'is_published',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Blog $blog): void {
            if (empty($blog->created_by)) {
                $blog->created_by = Auth::id();
            }
        });
    }
}
