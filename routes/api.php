<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ShopLocationController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\BlogController;

Route::prefix('v1')->group(function () {
	Route::prefix('products')->group(function () {
		Route::get('/', [ProductController::class, 'index']);
		Route::get('/new-arrival', [ProductController::class, 'new_arrival']);
		Route::get('/{slug}', [ProductController::class, 'detail']);
	});
	Route::get('/banners', [BannerController::class, 'index']);

	Route::prefix('categories')->group(function () {
		Route::get('/', [CategoryController::class, 'index']);
	});

	Route::prefix('brands')->group(function () {
		Route::get('/', [BrandController::class, 'index']);
	});

	Route::prefix('location')->group(function () {
		Route::get('/', [ShopLocationController::class,'index']);	
	});

	Route::prefix('promos')->group(function () {
		Route::get('/', [PromoController::class, 'index']);
		Route::get('/{slug}', [PromoController::class, 'detail']);
	});

	Route::prefix('blogs')->group(function () {
		Route::get('/', [BlogController::class, 'index']);
		Route::get('/recent', [BlogController::class, 'recent']);
		Route::get('/{slug}', [BlogController::class, 'detail']);
	});
});
