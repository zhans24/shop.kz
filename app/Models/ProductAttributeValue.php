<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','attribute_id','attribute_value_id','value',
    ];

    protected $casts = [
        'product_id' => 'int',
        'attribute_id' => 'int',
        'attribute_value_id' => 'int',
    ];

    public function product()  { return $this->belongsTo(Product::class); }
    public function attribute(){ return $this->belongsTo(Attribute::class); }
    public function attributeValue(){ return $this->belongsTo(AttributeValue::class); }
}
