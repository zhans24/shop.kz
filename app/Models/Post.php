<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSeo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, HasSeo;
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->useFallbackUrl('/img/no-image.webp');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(600)->height(400)->format('webp')->quality(82)->nonQueued();
        $this->addMediaConversion('large')->width(1200)->format('webp')->quality(80)->nonQueued();
    }

    protected $fillable = [
        'type','title','slug','excerpt','content',
        'is_published','published_at','data','sort',
    ];

    protected $casts = [
        'is_published' => 'bool',
        'published_at' => 'datetime',
        'data'         => 'array',
        'sort'         => 'int',
    ];

    // Скоупы
    public function scopePublished($q) {
        return $q->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
    public function scopeNews($q)  { return $q->where('type','news');  }
    public function scopePromos($q){ return $q->where('type','promo'); }

    public function endsAt(): ?\Carbon\Carbon {
        $v = data_get($this->data, 'ends_at');
        return $v ? \Carbon\Carbon::parse($v) : null;
    }
}
