<?php

namespace App\Support;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class PageData
{
    public static function getByTemplate(string $template): array
    {
        $key = self::cacheKey($template);

        return Cache::rememberForever($key, function () use ($template) {
            $page = Page::byTemplate($template);
            if (!$page) return ['template' => $template, 'exists' => false];

            $builder = self::builderFor($template);
            return $builder->build($page);
        });
    }

    public static function forgetByTemplate(string $template): void
    {
        Cache::forget(self::cacheKey($template));
    }

    private static function cacheKey(string $template): string
    {
        return 'pagedata:template:' . $template;
    }

    private static function builderFor(string $template): Contracts\PageBuilder
    {
        return match ($template) {
            'home'  => new PageBuilders\HomePageBuilder(),
            'about' => new PageBuilders\AboutPageBuilder(),
            'delivery' => new PageBuilders\DeliveryPageBuilder(),
            default => throw new \RuntimeException("No builder for template [$template]"),
        };


    }

    /** Сгруппировать медиа по section/slot/slide_uid с готовыми URL */
    public static function groupMedia(Page $page, string $collection = 'page_images'): array
    {
        $media = $page->getMedia($collection);
        $out = [];

        foreach ($media as $m) {
            $section  = data_get($m->custom_properties, 'section', '_');
            $slot     = data_get($m->custom_properties, 'slot');
            $slideUid = data_get($m->custom_properties, 'slide_uid');

            $url = null;
            foreach (['w1200_webp', 'webp', 'w1200'] as $conv) {
                try { $u = $m->getUrl($conv); if ($u) { $url = $u; break; } } catch (\Throwable) {}
            }
            $url ??= $m->getUrl();

            $out[$section][] = [
                'url'       => $url,
                'slot'      => $slot,
                'slide_uid' => $slideUid,
                'id'        => $m->id,
            ];
        }

        return $out;
    }
}
