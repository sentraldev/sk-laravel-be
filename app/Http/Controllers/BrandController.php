<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Http\Resources\BrandResource;


class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::get();

        return BrandResource::collection($brands);
    }
}
