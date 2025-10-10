<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;

Route::get('/products', [ProductController::class, 'index']);

Route::get('/banners', [BannerController::class, 'index']);
