<?php

namespace App\Http\Controllers\Front\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'brand', 'category',
                'attributesValues.attribute',
                'attributesValues.attributeValue',
                'stocks',
            ])
            ->firstOrFail();

        // Наличие
        $availability     = $product->availabilityByCity();
        $inStockSomewhere = $product->inStockSomewhere();

        // Галерея
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

        // Характеристики в удобном виде
        $specs = $product->attributesValues
            ? $product->attributesValues->map(function ($pv) {
                $attr = $pv->attribute;
                $name = $attr->name ?? $attr?->code ?? null;

                $val  = $pv->value
                    ?: $pv->value_text
                        ?: $pv->attributeValue?->value
                            ?: $pv->attributeValue?->slug
                                ?: null;

                if (!$name || !$val) return null;

                return [
                    'name'  => $name,
                    'value' => $val,
                    'code'  => $attr?->code,
                    'slug'  => $pv->attributeValue?->slug,
                ];
            })->filter()->values()
            : collect();

        $reviews = Review::query()
            ->where('product_id', $product->id)
            ->approved()
            ->with('media')
            ->latest('created_at')
            ->get();

        $reviewsCount = $reviews->count();

        // Похожие (та же категория)
        $similar = Product::query()
            ->published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('stocks')
            ->orderByDesc('published_at')
            ->limit(12)
            ->get();

        $typeValueText = $product->attributesValues()
            ->whereHas('attribute', fn($q)=>$q->where('code','type'))
            ->value('value_text');

        $typeValueId = $product->attributesValues()
            ->whereHas('attribute', fn($q)=>$q->where('code','type'))
            ->value('attribute_value_id');

        $recommended = Product::query()
            ->published()
            ->where('id', '!=', $product->id)
            ->when($typeValueText || $typeValueId, function ($q) use ($typeValueText, $typeValueId) {
                $q->whereHas('attributesValues', function ($w) use ($typeValueText, $typeValueId) {
                    $w->whereHas('attribute', fn($a)=>$a->where('code','type'))
                        ->where(function ($c) use ($typeValueText, $typeValueId) {
                            $c->when($typeValueText, fn($qq)=>$qq->where('value_text', $typeValueText))
                                ->orWhere('attribute_value_id', $typeValueId);
                        });
                });
            }, function ($q) use ($product) {
                $q->when($product->brand_id, fn($qq)=>$qq->where('brand_id', $product->brand_id))
                    ->when(!$product->brand_id, fn($qq)=>$qq->where('category_id', $product->category_id)->where('is_hit', true));
            })            ->with('stocks')

            ->orderByDesc('published_at')
            ->limit(12)
            ->get();

        // SEO
        $seo   = $product->seo ?? null;
        $title = $seo->meta_title ?? $product->meta_title ?? $product->name;
        $desc  = $seo->meta_description ?? $product->meta_description ?? null;
        $h1    = $seo->h1 ?? $product->name;

        return view('pages.product_show', compact(
            'product','availability','inStockSomewhere',
            'gallery','thumbs','specs',
            'similar','recommended',
            'title','desc','h1','reviews','reviewsCount'
        ));
    }

    public function storeReview(string $slug, Request $request)
    {
        $product = Product::query()->published()->where('slug', $slug)->firstOrFail();

        $v = Validator::make($request->all(), [
            'body'        => ['required', 'string', 'min:5'],
            'photos'      => ['nullable','array','max:5'],
            'photos.*'    => ['image', 'mimes:jpg,jpeg,png,webp,heic,avif', 'max:5120'],
        ], [], [
            'body'        => 'Текст отзыва',
            'photos'      => 'Фото',
            'photos.*'    => 'Фото',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $review = new Review();
            $review->product_id  = $product->id;
            $review->author_name = optional($request->user())->name ?: 'Не известно';
            $review->body        = (string)$request->input('body');
            $review->is_approved = false;
            $review->save();

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    if ($file && $file->isValid()) {
                        $review->addMedia($file)->toMediaCollection('photos');
                    }
                }
            }

            return back()->with('ok', 'Спасибо! Отзыв отправлен на модерацию.');
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withErrors(['common' => 'Не удалось сохранить отзыв. Попробуйте ещё раз.'])
                ->withInput();
        }
    }
}
