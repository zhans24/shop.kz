<?php

// app/Models/ProductStock.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'city_name', 'is_available'];

    protected $casts = [
        'product_id'   => 'int',
        'is_available' => 'bool',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
