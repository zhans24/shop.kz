<?php

namespace App\Http\Controllers\Front\Catalog;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->visible()
            ->orderBy('sort')
            ->paginate(24);

        $title = 'Категории';
        $h1    = 'Категории товаров';

        return view('pages.categories', compact('categories', 'title', 'h1'));
    }

    public function show(string $slug, Request $request)
    {
        $category = Category::query()->visible()->where('slug', $slug)->firstOrFail();

        $filters = [
            'brand_id'  => $request->integer('brand_id') ?: null,
            'type'      => $request->string('type')->toString() ?: null,
            'price_min' => $request->has('price_min') ? (float)$request->input('price_min') : null,
            'price_max' => $request->has('price_max') ? (float)$request->input('price_max') : null,
        ];
        $sort = $request->string('sort')->toString() ?: null;

        $products = $category->products()
            ->published()
            ->filter($filters)
            ->sortBy($sort)
            ->paginate(12)
            ->appends($request->query());

        // все бренды (как просил)
        $brands = \App\Models\Brand::query()
            ->orderBy('name')
            ->get(['id','name']);

        // все значения типа (как просил) — value/slug
        $types = \App\Models\AttributeValue::query()
            ->whereHas('attribute', fn($q)=>$q->where('code','type'))
            ->orderBy('value')
            ->get(['slug','value']);

        // динамический максимум цены (округляю вверх до 1000)
        $maxPriceInCat = (int) ceil((float) ($category->products()->published()->max('price') ?? 0) / 1000) * 1000;
        $priceMaxLimit = $maxPriceInCat > 0 ? $maxPriceInCat : 345000;

        $seo   = $category->seo ?? null;
        $title = $seo->meta_title ?? $category->meta_title ?? $category->name;
        $desc  = $seo->meta_description ?? $category->meta_description ?? null;
        $h1    = $seo->h1 ?? $category->name;

        return view('pages.category_show', compact(
            'category','products','brands','types','title','desc','h1','filters','sort','priceMaxLimit'
        ));
    }
}
