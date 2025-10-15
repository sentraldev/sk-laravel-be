<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;

Route::prefix('v1')->group(function () {
	Route::prefix('products')->group(function () {
		Route::get('/', [ProductController::class, 'index']);
		Route::get('/new-arrival', [ProductController::class, 'new_arrival']);
	});
	Route::get('/banners', [BannerController::class, 'index']);

	Route::prefix('categories')->group(function () {
		Route::get('/', [CategoryController::class, 'index']);
	});
});
