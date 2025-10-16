<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id','slug','name','is_visible','sort'];

    protected $casts = [
        'parent_id' => 'int',
        'is_visible' => 'bool',
        'sort' => 'int',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Удобный скоуп
    public function scopeVisible($q) { return $q->where('is_visible', true); }
}
