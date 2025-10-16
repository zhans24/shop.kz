<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id','value','slug','sort'];

    protected $casts = [
        'attribute_id' => 'int',
        'sort' => 'int',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function productValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
