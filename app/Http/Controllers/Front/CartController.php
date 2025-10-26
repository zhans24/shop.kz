<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Product $product, Request $request)
    {
        return back()->with('ok', 'Товар добавлен в корзину (front-localStorage).');
    }
}
