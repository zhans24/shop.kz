<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','author_name','rating','body','photos','is_approved'
    ];

    protected $casts = [
        'product_id' => 'int',
        'rating' => 'int',
        'photos' => 'array',
        'is_approved' => 'bool',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($q) { return $q->where('is_approved', true); }
}
