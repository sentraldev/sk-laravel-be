<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCenter extends Model
{
    protected $fillable = ['brand_id','name','address','phone','lat','lng'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
