<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $banners = Banner::where('is_active', true)
            ->latest()
            ->limit(10)
            ->get();

        return BannerResource::collection($banners);
    }
}
