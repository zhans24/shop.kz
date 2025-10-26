<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory;
    use HasSeo;
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->useFallbackUrl('/img/no-image.webp');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(400)->height(300)->format('webp')->quality(82)->nonQueued();
        $this->addMediaConversion('large')->width(1000)->format('webp')->quality(80)->nonQueued();
    }

    protected $fillable = ['parent_id','slug','name','is_visible','is_popular','sort'];
    protected $with = ['media'];

    protected $casts = [
        'parent_id' => 'int',
        'is_visible' => 'bool',
        'sort' => 'int',
        'is_popular' => 'bool',
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
