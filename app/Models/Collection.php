<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = ['key','name','type','rule'];

    protected $casts = [
        'rule' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product')
            ->withTimestamps()
            ->withPivot('sort')
            ->orderBy('pivot_sort');
    }

    public function scopeKey($q, string $key) { return $q->where('key', $key); }
}
