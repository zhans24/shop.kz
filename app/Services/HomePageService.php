<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class HomePageService
{
    public const KEYS = [
        'home:hits','home:promos','home:newest','home:news',
    ];

    public function get(int $ttlMinutes = 10): array
    {
        $ttl = now()->addMinutes($ttlMinutes);

        $hits = Cache::remember('home:hits', $ttl, fn () =>
        $this->mapProducts(
            Product::query()
                ->published()
                ->where('is_hit', true)
                ->orderByRaw('CASE WHEN hit_sort IS NULL THEN 1 ELSE 0 END, hit_sort ASC')
                ->orderByDesc('published_at')
                ->select(['id','slug','name','price','discount_percent','discount_is_forever','discount_starts_at','discount_ends_at'])
                ->with(['media' => fn ($q) => $q
                    ->whereIn('collection_name', ['cover','images'])
                ])
                ->limit(16)->get()
        )
        );

        $promos = Cache::remember('home:promos', $ttl, fn () =>
        $this->mapProducts(
            Product::query()
                ->published()
                ->withActiveDiscount()
                ->orderByDesc('discount_percent')
                ->orderByDesc('published_at')
                ->select(['id','slug','name','price','discount_percent','discount_is_forever','discount_starts_at','discount_ends_at'])
                ->with(['media' => fn ($q) => $q
                    ->whereIn('collection_name', ['cover','images'])
                ])
                ->limit(16)->get()
        )
        );

        $newest = Cache::remember('home:newest', $ttl, fn () =>
        $this->mapProducts(
            Product::query()
                ->published()
                ->orderByDesc('published_at')
                ->select(['id','slug','name','price','discount_percent','discount_is_forever','discount_starts_at','discount_ends_at'])
                ->with(['media' => fn ($q) => $q
                    ->whereIn('collection_name', ['cover','images'])
                ])
                ->limit(3)->get()
        )
        );

        $news = Cache::remember('home:news', $ttl, fn () =>
        Post::query()
            ->published()->news()
            ->orderByDesc('published_at')
            ->select(['id','slug','title','excerpt','published_at'])
            ->with(['media' => fn ($q) => $q
                ->where('collection_name', 'cover')
            ])
            ->limit(6)->get()
            ->map(fn($p) => [
                'slug'   => $p->slug,
                'title'  => $p->title,
                'date'   => optional($p->published_at)->format('d.m.Y H:i'),
                'img'    => $p->getFirstMediaUrl('cover','thumb') ?: $p->getFirstMediaUrl('cover'),
                'excerpt'=> $p->excerpt,
            ])
            ->all()
        );

        return compact('hits','promos','newest','news');
    }

    private function mapProducts($collection): array
    {
        return $collection->map(function ($p) {
            return [
                'slug'         => $p->slug,
                'name'         => $p->name,
                'img_thumb'    => $p->coverUrl('thumb'),
                'img_large'    => $p->coverUrl('large'),
                'price'        => (float) $p->price,
                'discount_pct' => (int) ($p->discount_percent ?? 0),
                'final_price'  => $p->finalPrice(),
                'is_hit'       => (bool) ($p->is_hit ?? false),
            ];
        })->all();
    }


    public static function forgetAll(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
