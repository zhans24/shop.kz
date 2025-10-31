<?php

namespace App\Services;

use App\Models\{Order, Product, DeliveryMethod, PaymentMethod};
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function place(array $items, array $form, ?int $userId = null): Order
    {
        Log::debug('checkout.place:start', [
            'user_id'     => $userId,
            'items_count' => count($items),
            'codes'       => collect($items)->pluck('code')->all(),
        ]);

        return DB::transaction(function () use ($items, $form, $userId) {
            $delivery = DeliveryMethod::query()->where('is_active', true)->findOrFail($form['delivery_method_id']);
            $payment  = PaymentMethod::query()->where('is_active', true)->findOrFail($form['payment_method_id']);

            $codes = collect($items)->pluck('code')->filter()->unique()->values()->all();

            $products = Product::query()
                ->whereIn('sku', $codes)
                ->orWhereIn('slug', $codes)
                ->get()
                ->keyBy(fn($p) => $p->sku ?: $p->slug);

            $foundKeys   = $products->keys()->all();
            $missing     = array_values(array_diff($codes, $foundKeys));

            Log::debug('checkout.place:resolved-products', [
                'requested_codes' => $codes,
                'found'           => $foundKeys,
                'missing'         => $missing,
            ]);

            $order = Order::create([
                'user_id'               => $userId,
                'delivery_method_id'    => $delivery->id,
                'delivery_method_name'  => $delivery->name,
                'shipping_total' => (float) round($delivery->price, 0),
                'payment_method_id'     => $payment->id,
                'payment_method_name'   => $payment->name,
                'customer_type'         => $form['customer_type'] ?? 'private',
                'contact_name'          => $form['contact_name'] ?? null,
                'phone'                 => $form['phone'] ?? null,
                'address'               => $form['address'] ?? null,
                'status'                => 'new',
            ]);

            foreach ($items as $row) {
                $code = (string)($row['code'] ?? '');
                $qty  = max(1, (int)($row['qty'] ?? 1));

                $product = $products->get($code);
                if (!$product) {
                    Log::warning('checkout.place:item-skipped-missing', ['code' => $code, 'qty' => $qty]);
                    continue;
                }

                $price = (float) round($product->finalPrice(), 0);

                $order->items()->create([
                    'product_id' => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name,
                    'qty'        => $qty,
                    'price'      => $price,
                ]);

                Log::debug('checkout.place:item-added', [
                    'order_id'   => $order->id,
                    'code'       => $code,
                    'qty'        => $qty,
                    'price'      => $price,
                ]);
            }

            $order->recalcTotals();

            Log::info('checkout.place:totals', [
                'order_id'       => $order->id,
                'items_subtotal' => (float) $order->items_subtotal,
                'shipping_total' => (float) $order->shipping_total,
                'total'          => (float) $order->total,
                'items_count'    => $order->items()->count(),
            ]);

            return $order->load('items');
        });
    }
}
