<?php

namespace App\Providers;

use App\Models\Page;
use App\Support\PageData;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

    }
}
