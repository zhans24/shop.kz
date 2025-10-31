<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;

class OrdersHistoryController extends Controller
{
    public function index()
    {
        $items = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['product.media', 'order'])
            ->latest('id')
            ->cursorPaginate(12);

        $title = 'История заказов';


        return view('pages.history', compact('items', 'title'));
    }
}
