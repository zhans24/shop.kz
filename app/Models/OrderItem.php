<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','product_id',
        'sku','name','qty','price','total',
    ];

    protected $casts = [
        'qty'   => 'int',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }

    protected static function booted(): void
    {
        static::saving(function (self $i) {
            $i->total = (float)$i->price * (int)$i->qty;
        });
    }
}
