<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    public function index()
    {
        return view('pages.cart');
    }

    public function add(): RedirectResponse
    {
        return back()->with('ok', 'Товар добавлен в корзину.');
    }

    /**
     * Унифицированный "гидратор" — если где-то зовёте /ajax/cart-hydrate,
     * просто отдаём то же, что и syncShow (мета-обогащённые items).
     */
    public function hydrate(Request $request)
    {
        return $this->syncShow($request);
    }

    /**
     * GET /cart/sync
     * Отдаём серверную корзину пользователя, обогащённую полями товара.
     */
    public function syncShow(Request $request)
    {
        $user = $request->user();

        // На всякий — пустой ответ гостю
        if (!$user) {
            return response()->json(['items' => [], 'updated_at' => null]);
        }

        $cart = Cart::forUser($user->id);

        $items = $cart->items ?? [];
        $codes = collect($items)->pluck('code')->filter()->unique()->values()->all();

        $products = Product::query()
            ->whereIn('sku', $codes)
            ->get()
            ->keyBy('sku');

        $fmt = fn($n) => number_format((int)round($n, 0), 0, ',', ' ') . ' ₸';

        $enriched = collect($items)->map(function ($row) use ($products, $fmt) {
            $code = (string) Arr::get($row, 'code', '');
            $qty  = max(1, (int) Arr::get($row, 'qty', 1));
            $p    = $products->get($code);

            $priceFinal = $p ? (int) round($p->finalPrice(), 0) : null;
            $priceBase  = $p ? (int) round((float) $p->price, 0) : null;

            return [
                'code'      => $code,
                'qty'       => $qty,
                'name'      => $p?->name ?? null,
                'image'     => $p?->coverUrl('thumb') ?? null,
                'newPrice'  => $p ? $fmt($priceFinal) : null,
                'oldPrice'  => ($p && $p->hasActiveDiscount()) ? $fmt($priceBase) : null,
            ];
        })->values()->all();

        return response()->json([
            'items'      => $enriched,
            'updated_at' => optional($cart->updated_at)->toIso8601String(),
        ]);
    }

    /**
     * PUT /cart/sync
     * Принимаем items от клиента.
     * merge=false — перезапись, merge=true — объединение.
     */
    public function syncUpdate(Request $request)
    {
        $data = $request->validate([
            'items' => ['array'],
            'items.*.code' => ['required', 'string', 'max:255'],
            'items.*.qty'  => ['required', 'integer', 'min:1', 'max:9999'],
            'merge'        => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $cart = Cart::forUser($user->id);

        $incoming = collect($data['items'] ?? [])
            ->map(fn($i) => ['code' => (string)$i['code'], 'qty' => (int)max(1, $i['qty'])]);
        $existing = collect($cart->items ?? [])
            ->map(fn($i) => ['code' => (string)Arr::get($i, 'code', ''), 'qty' => (int)max(1, Arr::get($i, 'qty', 1))]);

        if (!empty($data['merge'])) {
            // Без удвоений: для одинаковых кодов берем максимум qty
            $map = [];
            foreach ($existing as $row)   { $map[$row['code']] = max($map[$row['code']] ?? 0, $row['qty']); }
            foreach ($incoming as $row)   { $map[$row['code']] = max($map[$row['code']] ?? 0, $row['qty']); }
            $merged = [];
            foreach ($map as $code => $qty) { $merged[] = ['code' => $code, 'qty' => (int)$qty]; }
            $cart->items = array_values($merged);
        } else {
            // Полная перезапись
            $cart->items = $incoming->values()->all();
        }

        $cart->save();

        return response()->json(['ok' => true, 'items' => $cart->items]);
    }
}
