<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\Catalog\CategoryController;
use App\Http\Controllers\Front\Catalog\ProductController;
use App\Http\Controllers\Front\Pages\AboutController;
use App\Http\Controllers\Front\Pages\DeliveryController;
use App\Http\Controllers\Front\Pages\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::name('front.')->group(function () {
    Route::get('/',        [HomeController::class,    'index'])->name('home');
    Route::get('/about',   [AboutController::class,   'index'])->name('about');
    Route::get('/delivery',[DeliveryController::class,'index'])->name('delivery');
    Route::view('/privacy', 'pages.privacy')->name('privacy');
});

Route::get('/product/{slug}', [ProductController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-\_]+')
    ->name('product.show');

Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');

Route::get('/category/{slug}', [CategoryController::class, 'show'])
    ->name('category.show');


Route::view('/cart', 'pages.cart')->name('cart.index');

Route::post('/cart/add/{product}', [CartController::class, 'add'])
    ->name('cart.add');


