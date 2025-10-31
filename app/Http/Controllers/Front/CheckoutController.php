<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function submit(CheckoutRequest $request, CheckoutService $svc): RedirectResponse
    {
        // Корреляция для всего запроса
        $reqId = (string) Str::uuid();
        Log::withContext(['req_id' => $reqId]);

        $data  = $request->validated();
        $items = $data['items'];

        Log::info('checkout.submit:start', [
            'req_id'   => $reqId,
            'user_id'  => auth()->id(),
            'ip'       => $request->ip(),
            'payload'  => [
                'delivery_method_id' => $data['delivery_method_id'] ?? null,
                'payment_method_id'  => $data['payment_method_id'] ?? null,
                'customer_type'      => $data['customer_type'] ?? null,
                'contact_name'       => $data['contact_name'] ?? null,
                'phone'              => $data['phone'] ?? null, // если надо — замаскируй
                'address'            => $data['address'] ?? null,
                'client_total'       => $data['client_total'] ?? null,
                'items_count'        => is_array($items) ? count($items) : 0,
                'items_preview'      => array_slice($items, 0, 5),
            ],
        ]);

        try {
            DB::beginTransaction();

            $order = $svc->place($items, $data, auth()->id());

            if (!empty($data['client_total'])) {
                $client = (int) round((float)$data['client_total'], 0);
                $server = (int) round((float)$order->total, 0);

                if ($client !== $server) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['checkout' => 'Сумма изменилась. Обновите страницу и попробуйте снова.'])
                        ->withInput();
                }
            }

            DB::commit();

            if (auth()->check()) {
                Cart::query()->where('user_id', auth()->id())->update(['items' => []]);
            }
            $message = 'Заказ отправлен. Наш менеджер свяжется с вами.';

            if (auth()->check()) {
                return redirect()->route('orders.history')->with('ok', $message);
            }

            return redirect()->route('front.home')->with('ok', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }
    }
}

