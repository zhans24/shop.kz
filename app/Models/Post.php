<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasSeo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, HasSeo, InteractsWithMedia;

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

    protected $with = ['media','seo'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(600)->height(400)->format('webp')->quality(82)->nonQueued();
        $this->addMediaConversion('large')->width(1200)->format('webp')->quality(80)->nonQueued();
    }

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

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $base = $post->slug ?: Str::slug((string) $post->title);
            $post->slug = static::makeUniqueSlug($base);

            if ($post->is_published && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function (Post $post) {
            if (empty($post->slug)) {
                $base = Str::slug((string) $post->title);
                $post->slug = static::makeUniqueSlug($base, $post->id);
            } else {
                $base = Str::slug((string) $post->slug);
                $post->slug = static::makeUniqueSlug($base, $post->id);
            }

            if ($post->is_published && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    protected static function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: Str::random(6);

        $i = 2;
        while (static::slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i;
            $i++;
        }
        return $slug;
    }

    protected static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $q = static::query()->where('slug', $slug);
        if ($ignoreId) $q->where('id', '!=', $ignoreId);
        return $q->exists();
    }
}
