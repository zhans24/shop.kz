<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    protected $fillable = ['name','price','is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'bool',
    ];

    public function scopeActive($q) {
        return $q->where('is_active', true);
    }
}
