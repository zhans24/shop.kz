<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Services\HomePageService;
use App\Support\Cart;
use App\Support\PageData;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('cart', fn() => new Cart());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $forgetPageData = function (Media $m) {
            if ($m->model_type !== Page::class) return;
            $page = $m->model;
            PageData::forgetByTemplate((string) $page->template);
        };

        Media::saved($forgetPageData);
        Media::deleted($forgetPageData);


        View::composer(['partials.header', 'partials.footer', 'pages.contacts'], function ($view) {
            $view->with('contacts', site_contacts());
        });

        View::composer(['partials.header'], function ($view) {
            $page = Page::byTemplate('header');

            $raw = (array) data_get($page?->content, 'header.cities', []);
            $cities = collect($raw)
                ->map(fn ($r) => [
                    'name'       => trim((string) data_get($r, 'name')),
                    'slug'       => Str::slug((string) data_get($r, 'slug', data_get($r, 'name'))),
                    'sort'       => (int) data_get($r, 'sort', 100),
                    'is_default' => (bool) data_get($r, 'is_default', false),
                ])
                ->filter(fn ($r) => $r['name'] !== '' && $r['slug'] !== '')
                ->sortBy('sort')
                ->values()
                ->all();

            $sessionSlug = session('city');
            $bySlug = collect($cities)->firstWhere('slug', $sessionSlug);
            $byDefault = collect($cities)->firstWhere('is_default', true);
            $current = $bySlug ?? $byDefault ?? (Arr::first($cities) ?: ['name' => 'Алматы', 'slug' => 'almaty']);

            $view->with([
                'headerCities' => $cities,
                'currentCity'  => $current,
            ]);
        });


        Relation::morphMap([
            'product'  => Product::class,
            'category' => Category::class,
            'post'     => Post::class,
            'page'     => Page::class,
        ]);

        Product::saved(fn () => HomePageService::forgetAll());
        Product::deleted(fn () => HomePageService::forgetAll());

        Post::saved(fn () => HomePageService::forgetAll());
        Post::deleted(fn () => HomePageService::forgetAll());
    }
}
