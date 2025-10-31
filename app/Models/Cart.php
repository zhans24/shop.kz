<?php

// app/Models/Cart.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id','items'];
    protected $casts = ['items' => 'array'];

    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId], ['items' => []]);
    }
}

