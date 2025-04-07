<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\HomeController; // Added AttributeController
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Home page route
Route::get('home', [HomeController::class, 'index']);

// Auth routes
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

// Category routes
Route::apiResource('categories', CategoryController::class);
Route::get('categories/{category}/products', [CategoryController::class, 'products']);

// Attribute routes
Route::apiResource('attributes', AttributeController::class);

// Product routes
Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index');
    Route::get('products/{product}', 'show');
    Route::post('products', 'store');
    Route::put('products/{product}', 'update');
    Route::delete('products/{product}', 'destroy');
    
    // Favorite routes
    Route::post('products/{product}/favorite', 'toggleFavorite');
    Route::get('favorites', 'favorites');
    
    // User products
    Route::get('my-products', 'userProducts');
});
