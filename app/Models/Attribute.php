<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['code','name','type','unit','is_filterable','sort'];

    protected $casts = [
        'is_filterable' => 'bool',
        'sort' => 'int',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function productValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function scopeFilterable($q) { return $q->where('is_filterable', true); }
}
