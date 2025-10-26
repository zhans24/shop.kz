<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function submit(CheckoutRequest $request, CheckoutService $svc)
    {
        $data   = $request->validated();
        $cart   = app('cart')->items(); // твой источник корзины

        try {
            DB::beginTransaction();

            $order = $svc->place($cart, $data, auth()->id());

            if (!empty($data['client_total'])) {
                $client = round((float) $data['client_total'], 2);
                $server = (float) $order->total;
                if (abs($client - $server) > 0.01) {
                    throw new \RuntimeException('Сумма изменилась. Обновите страницу и попробуйте снова.');
                }
            }

            // Никаких оплат. Статус остаётся 'new'
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }

        return redirect()->route('profile.orders.history')
            ->with('ok', 'Заказ отправлен. Наш менеджер свяжется с вами.');
    }
}
