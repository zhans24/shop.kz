<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'delivery_method_id','delivery_method_name',
        'payment_method_id','payment_method_name',
        'shipping_total',
        'customer_type','contact_name','phone','address',
        'status',
        'items_subtotal','total',
    ];

    protected $casts = [
        'user_id'             => 'int',
        'delivery_method_id'  => 'int',
        'payment_method_id'   => 'int',
        'shipping_total'      => 'decimal:2',
        'items_subtotal'      => 'decimal:2',
        'total'               => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function recalcTotals(): void
    {
        $itemsSum = $this->items->reduce(fn($s,$it)=> $s + ((float)$it->price * (int)$it->qty), 0.0);

        $itemsSumInt = (int) round($itemsSum, 0);
        $shippingInt = (int) round((float)($this->shipping_total ?? 0), 0);
        $grandInt    = $itemsSumInt + $shippingInt;

        $this->update([
            'items_subtotal' => $itemsSumInt,
            'total'          => $grandInt,
        ]);
    }

}
