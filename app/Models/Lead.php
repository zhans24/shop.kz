<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeNew($q)      { return $q->where('status', 'new'); }
    public function scopeInProgress($q) { return $q->where('status', 'in_progress'); }
    public function scopeDone($q)     { return $q->where('status', 'done'); }
}
