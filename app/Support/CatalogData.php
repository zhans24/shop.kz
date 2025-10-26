<?php

namespace App\Support;

use App\Models\{Category, Brand, Product, ProductAttributeValue, Attribute};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

final class CatalogData
{
    // ===== КАТАЛОГ: СПИСОК КАТЕГОРИЙ =====
    public static function categoriesIndexTiles(): array
    {
        $key = 'catalog:categories_index:tiles';

        return Cache::rememberForever($key, function () {
            return Category::query()
                ->select(['id','name','slug'])
                ->orderBy('sort')->orderBy('name')
                ->get()
                ->map(fn($c) => [
                    'id'   => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'img'  => $c->getFirstMediaUrl('image', 'thumb') ?: $c->getFirstMediaUrl('image'),
                    'url'  => route('category.show', $c->slug),
                ])
                ->all();
        });
    }

    // Удобно, если хочешь пагинировать именно массив тайлов
    public static function paginateTiles(array $tiles, int $perPage = 12): LengthAwarePaginator
    {
        $page    = request()->integer('page', 1);
        $offset  = ($page - 1) * $perPage;
        $slice   = array_slice($tiles, $offset, $perPage);

        return new LengthAwarePaginator($slice, count($tiles), $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }

    // ===== СТРАНИЦА КАТЕГОРИИ: СПРАВОЧНИКИ ДЛЯ ФИЛЬТРА =====
    public static function categoryFacets(int $categoryId): array
    {
        $key = "catalog:category_show:{$categoryId}:facets";

        return Cache::rememberForever($key, function () use ($categoryId) {
            // бренды, реально присутствующие в этой категории
            $brandRows = Brand::query()
                ->whereIn('id', Product::query()
                    ->where('category_id', $categoryId)
                    ->published()
                    ->pluck('brand_id')
                    ->filter()
                    ->unique()
                )->orderBy('name')
                ->get(['id','name']);

            // типы (из attribute code=type), реально присутствующие
            $typeAttrId = Attribute::query()->where('code','type')->value('id');
            $typeRows = ProductAttributeValue::query()
                ->where('attribute_id', $typeAttrId)
                ->whereIn('product_id', Product::query()
                    ->where('category_id', $categoryId)
                    ->published()
                    ->pluck('id')
                )
                ->selectRaw("COALESCE(value_text, '') as value_text")
                ->distinct()
                ->orderBy('value_text')
                ->get()
                ->map(fn($r) => [
                    'value' => $r->value_text,
                    'slug'  => str($r->value_text)->slug()->value(),
                ]);

            // верхняя граница цены по категории
            $priceMaxLimit = (int) Product::query()
                ->where('category_id', $categoryId)
                ->published()
                ->max('price') ?: 0;

            return [
                'brands'         => $brandRows->map(fn($b)=>['id'=>$b->id,'name'=>$b->name])->all(),
                'types'          => $typeRows->all(),
                'priceMaxLimit'  => max(0, $priceMaxLimit),
            ];
        });
    }

    // ===== Инвалидация =====
    public static function forgetCategoriesIndex(): void
    {
        Cache::forget('catalog:categories_index:tiles');
    }

    public static function forgetCategoryFacets(int $categoryId): void
    {
        Cache::forget("catalog:category_show:{$categoryId}:facets");
    }
}
