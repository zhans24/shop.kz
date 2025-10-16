<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','sku','price','price_old','quantity',
        'options','is_active','sort',
    ];

    protected $casts = [
        'product_id' => 'int',
        'price' => 'decimal:2',
        'price_old' => 'decimal:2',
        'quantity' => 'int',
        'options' => 'array', // JSON -> array
        'is_active' => 'bool',
        'sort' => 'int',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        // При изменении вариантов — пересчёт min_price у товара
        static::saved(function (self $variant) {
            $variant->product?->recalcMinPrice();
        });
        static::deleted(function (self $variant) {
            $variant->product?->recalcMinPrice();
        });
    }
}
