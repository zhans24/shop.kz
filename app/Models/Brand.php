<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','country','is_visible','sort'];

    protected $casts = [
        'is_visible' => 'bool',
        'sort' => 'int',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
