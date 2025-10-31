<?php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Services\HomePageService;
use App\Support\PageData;

class PagesController extends Controller
{
    public function home(HomePageService $service)
    {
        $categories = Category::query()
            ->visible()
            ->where('is_popular', true)
            ->orderBy('sort')
            ->take(12)
            ->get();

        $sections = $service->get(10);



        $page    = Page::byTemplate('home');
        $homeDto = PageData::getByTemplate('home');

        return view('pages.home', array_merge(
            compact('page','homeDto','categories'),
            $sections
        ));
    }

    public function show(string $template)
    {
        $data = PageData::getByTemplate($template);
        abort_unless(($data['exists'] ?? false) === true, 404);

        // имя вьюхи = template
        return view('pages.' . $template, $data);
    }
}
