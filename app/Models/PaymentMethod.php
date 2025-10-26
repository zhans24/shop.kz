<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name','is_active'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function scopeActive($q) {
        return $q->where('is_active', true);
    }
}
