<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $categories = Category::get();

        return CategoryResource::collection($categories);
    }

}
