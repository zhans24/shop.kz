<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id','brand_id','name','slug','short_desc','description',
        'rating','is_published','published_at','min_price',
    ];

    protected $casts = [
        'category_id' => 'int',
        'brand_id' => 'int',
        'rating' => 'decimal:1',
        'is_published' => 'bool',
        'published_at' => 'datetime',
        'min_price' => 'decimal:2',
    ];

    // --- Relations
    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }

    public function variants() { return $this->hasMany(ProductVariant::class); }

    public function attributesValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function reviews() { return $this->hasMany(Review::class); }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product')
            ->withTimestamps()
            ->withPivot('sort')
            ->orderBy('pivot_sort');
    }

    // --- Scopes
    public function scopePublished($q)
    {
        return $q->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // --- Helpers
    public function recalcMinPrice(): void
    {
        $min = $this->variants()->where('is_active', true)->min('price');
        $this->forceFill(['min_price' => $min])->saveQuietly();
    }
}
