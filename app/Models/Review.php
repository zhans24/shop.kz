<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Review extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'product_id','author_name','body','is_approved'
    ];

    protected $casts = [
        'product_id' => 'int',
        'is_approved' => 'bool',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($q)
    {
        return $q->where('is_approved', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->useDisk('public')
            ->onlyKeepLatest(5); // максимум 5 фото на отзыв
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->height(600)
            ->format('webp')
            ->quality(80)
            ->nonQueued();
    }
}
