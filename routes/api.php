<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;

Route::prefix('v1')->group(function () {
	Route::prefix('products')->group(function () {
		Route::get('/', [ProductController::class, 'index']);
		Route::get('/new-arrival', [ProductController::class, 'new_arrival']);
	});
	Route::get('/banners', [BannerController::class, 'index']);

	Route::prefix('categories')->group(function () {
		Route::get('/', [CategoryController::class, 'index']);
	});

	Route::prefix('brands')->group(function () {
		Route::get('/', [BrandController::class, 'index']);
	});
});
