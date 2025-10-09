<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $products = Product::with(['brand', 'laptop'])
            ->where('is_active', true)
            ->latest()
            ->paginate(20);

        return ProductResource::collection($products);
    }
}
