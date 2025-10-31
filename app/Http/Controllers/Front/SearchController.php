<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // /search?q=...
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $products = Product::query()
            ->published()
            ->when($q !== '', function ($qBuilder) use ($q) {
                $term = mb_strtolower($q);
                $qBuilder->where(function ($w) use ($term) {
                    $w->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$term}%"]);
                });
            })
            ->orderBy('published_at','desc')
            ->paginate(12)
            ->appends(['q' => $q]);

        $title = $q ? "Поиск: {$q}" : 'Поиск по каталогу';
        $desc  = null;
        $h1    = 'Результаты поиска';

        return view('pages.search', compact('products','q','title','desc','h1'));
    }

    // /ajax/suggest?s=...
    public function suggest(Request $request)
    {
        $s = trim((string)$request->query('s', ''));
        if (mb_strlen($s) < 2) {
            return response()->json([]);
        }

        $term = mb_strtolower($s);
        $rows = Product::query()
            ->published()
            ->where(function ($w) use ($term) {
                $w->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$term}%"]);
            })
            ->orderBy('published_at','desc')
            ->limit(8)
            ->get();

        $out = $rows->map(function ($p) {
            return [
                'name'  => $p->name,
                'sku'   => $p->sku,
                'price' => number_format($p->finalPrice(), 0, '.', ' ') . ' ₸',
                'img'   => $p->coverUrl('thumb') ?? asset('img/not.png'),
                'url'   => route('product.show', $p->slug),
            ];
        });

        return response()->json($out);
    }
}
