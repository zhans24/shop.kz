<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;

class OrdersHistoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $items = OrderItem::query()
            ->select([
                'order_items.id',
                'order_items.name',
                'order_items.price',
                'order_items.qty',
                'orders.created_at as ordered_at',
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->orderByDesc('orders.created_at')
            ->orderByDesc('order_items.id')
            ->cursorPaginate(20);

        return view('profile.orders.history', compact('items'));
    }
}
