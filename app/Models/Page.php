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
        // существующие
        $this->addMediaCollection('hero_left');
        $this->addMediaCollection('hero_right');

        // ABOUT: единая коллекция (фото ИЛИ видео)
        $this->addMediaCollection('about_media')->singleFile();

        // доставка
        $this->addMediaCollection('delivery_image');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // конвертации только для изображений
        if ($media && str_starts_with((string) $media->mime_type, 'image/')) {
            $this->addMediaConversion('webp')
                ->format('webp')
                ->quality(85)
                ->nonQueued();
        }
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
