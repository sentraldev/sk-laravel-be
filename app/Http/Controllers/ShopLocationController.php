<?php

namespace App\Http\Controllers;

use App\Models\ShopLocation;
use Illuminate\Http\Request;
use App\Http\Resources\ShopLocationResource;

class ShopLocationController extends Controller
{
    public function index(Request $request)
    {
        $products = ShopLocation::get();

        return ShopLocationResource::collection($products);
    }
}
