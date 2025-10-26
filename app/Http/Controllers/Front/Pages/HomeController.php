<?php
namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Support\PageData;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->visible()
            ->where('is_popular', true)
            ->orderBy('sort')
            ->take(12)
            ->get();

        $hits = Collection::key('hits')->first()?->products()
            ->published()->take(12)->get() ?? collect();

        $promos = Product::published()->withActiveDiscount()->take(12)->get();

        $newest = Product::published()->latest('published_at')->take(3)->get();

        $news = Post::published()->news()->latest('published_at')->take(6)->get();

        $page    = Page::byTemplate('home');
        $homeDto = PageData::getByTemplate('home');

        return view('pages.home', compact(
            'categories','hits','promos','newest','news','page','homeDto'
        ));
    }
}
