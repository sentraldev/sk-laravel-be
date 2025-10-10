<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;

Route::prefix('v1')->group(function () {
	Route::get('/products', [ProductController::class, 'index']);
	Route::get('/banners', [BannerController::class, 'index']);
});
