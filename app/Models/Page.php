<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Page extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title','slug','template','is_published',
        'meta_title','meta_description','content',
    ];

    protected $casts = [
        'is_published' => 'bool',
        'content'      => 'array',
    ];

    public function section(string $key, $default = null) {
        return data_get($this->content, $key, $default);
    }

    public function scopePublished($q) {
        return $q->where('is_published', true);
    }

    public static function byTemplate(string $template): ?self {
        return Cache::remember("page:template:$template", 86400, fn() =>
        static::query()->published()->where('template', $template)->first()
        );
    }

    public function registerMediaCollections(): void
    {
        // уже были:
        $this->addMediaCollection('hero_left');
        $this->addMediaCollection('hero_right');

        // ABOUT (только картинка)
        $this->addMediaCollection('about_image');

        $this->addMediaCollection('delivery_image');            // шапка/картинка секции

    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->quality(85)
            ->performOnCollections(
                'hero_left', 'hero_right',
                'about_image',            'delivery_image'
            )
            ->nonQueued();
    }



    protected static function booted(): void {
        $forget = function (self $p) {
            Cache::forget('page:template:' . (string) $p->template);
            \App\Support\PageData::forgetByTemplate((string) $p->template);
        };
        static::saved($forget);
        static::deleted($forget);
    }
}
