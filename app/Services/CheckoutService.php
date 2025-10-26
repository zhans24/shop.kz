<?php

namespace App\Services;

use App\Models\{Order, Product, DeliveryMethod, PaymentMethod};
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function place(array $cart, array $form, ?int $userId = null): Order
    {
        return DB::transaction(function () use ($cart, $form, $userId) {
            $delivery = DeliveryMethod::active()->findOrFail($form['delivery_method_id']);
            $payment  = PaymentMethod::active()->findOrFail($form['payment_method_id']);

            $order = Order::create([
                'user_id'               => $userId,
                'delivery_method_id'    => $delivery->id,
                'delivery_method_name'  => $delivery->name,
                'shipping_total'        => $delivery->price,
                'payment_method_id'     => $payment->id,
                'payment_method_name'   => $payment->name,
                'customer_type'         => $form['customer_type'] ?? 'private',
                'contact_name'          => $form['contact_name'] ?? null,
                'phone'                 => $form['phone'] ?? null,
                'address'               => $form['address'] ?? null,
                'status'                => 'new',
            ]);

            foreach ($cart as $row) {
                $product = Product::findOrFail($row['product_id']);
                $qty     = max(1, (int)($row['qty'] ?? 1));
                $price   = (float) ($product->price ?? 0);

                $order->items()->create([
                    'product_id' => $product->id,
                    'sku'        => $product->slug,
                    'name'       => $product->name,
                    'qty'        => $qty,
                    'price'      => $price,
                ]);
            }

            $order->recalcTotals();
            return $order->load('items');
        });
    }
}
