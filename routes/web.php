<?php

use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\Catalog\CategoryController;
use App\Http\Controllers\Front\Catalog\ProductController;
use App\Http\Controllers\Front\LeadController;
use App\Http\Controllers\Front\Pages\CityController;
use App\Http\Controllers\Front\Pages\PagesController;
use App\Http\Controllers\Front\Pages\PostsController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Front\Account\OrdersHistoryController;
use Illuminate\Support\Facades\Route;

// /dashboard всегда на главную
Route::get('/dashboard', fn () => redirect()->route('front.home'))->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // страница профиля
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // обновить профиль (имя, email, телефон)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // удалить аккаунт
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // история заказов
    Route::get('/profile/orders', [OrdersHistoryController::class, 'index'])
        ->name('orders.history');

    Route::get('/cart/sync', [CartController::class, 'syncShow'])->name('cart.sync.show');
    Route::put('/cart/sync', [CartController::class, 'syncUpdate'])->name('cart.sync.update');
});

require __DIR__.'/auth.php';

Route::name('front.')->group(function () {
    Route::get('/', [PagesController::class, 'home'])->name('home');
    Route::get('/about', [PagesController::class, 'show'])->defaults('template','about')->name('about');
    Route::get('/delivery', [PagesController::class, 'show'])->defaults('template','delivery')->name('delivery');
    Route::get('/privacy', [PagesController::class, 'show'])->defaults('template','privacy')->name('privacy');
    Route::get('/contacts', fn () => view('pages.contacts'))->name('contacts');

    Route::post('/contacts/send', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/set-city/{slug}', [CityController::class, 'set'])->name('set-city');
});

// Каталог/товары
Route::get('/product/{slug}', [ProductController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-\_]+')
    ->name('product.show');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');


Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/checkout', [CheckoutController::class, 'submit'])->name('checkout.submit');


Route::get('/news', [PostsController::class, 'index'])->name('posts.index');

Route::post('/product/{slug}/reviews', [ProductController::class, 'storeReview'])
    ->where('slug', '[A-Za-z0-9\-\_]+')
    ->middleware('auth')
    ->name('product.reviews.store');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/ajax/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

Route::get('/ajax/cart-hydrate', [CartController::class, 'hydrate'])
    ->name('cart.hydrate');
