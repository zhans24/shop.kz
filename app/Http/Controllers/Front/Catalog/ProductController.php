<?php

namespace App\Http\Controllers\Front\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'brand',
                'category',
                'attributesValues.attribute',
                'attributesValues.attributeValue',
                'reviews',        // если есть, просто подтянем; если нет — Blade покажет empty-state
            ])
            ->firstOrFail();

        // Галерея (первым — cover), форматы под твои Swiper-классы
        $hero = $product->coverUrl('large');
        $gallery = array_values(array_filter(array_merge(
            $hero ? [$hero] : [],
            $product->galleryUrls('large')
        )));

        $thumbCover = $product->coverUrl('thumb');
        $thumbs = array_values(array_filter(array_merge(
            $thumbCover ? [$thumbCover] : [],
            $product->galleryUrls('thumb')
        )));

        // Характеристики: имя атрибута + значение (value_text либо связанный AttributeValue->value/slug)
        $specs = $product->attributesValues
            ? $product->attributesValues->map(function ($pv) {
                $name = $pv->attribute->name ?? $pv->attribute?->code ?? null;
                $val  = $pv->value_text
                    ?? $pv->attributeValue->value
                    ?? $pv->attributeValue->slug
                    ?? null;
                if (!$name || !$val) return null;
                return ['name' => $name, 'value' => $val];
            })->filter()->values()
            : collect();

        // Похожие — из этой же категории (без текущего)
        $similar = Product::query()
            ->published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(12)
            ->get();

        // SEO
        $seo   = $product->seo ?? null;
        $title = $seo->meta_title ?? $product->meta_title ?? $product->name;
        $desc  = $seo->meta_description ?? $product->meta_description ?? null;
        $h1    = $seo->h1 ?? $product->name;

        return view('pages.product_show', compact(
            'product', 'gallery', 'thumbs', 'specs', 'similar', 'title', 'desc', 'h1'
        ));
    }
}
