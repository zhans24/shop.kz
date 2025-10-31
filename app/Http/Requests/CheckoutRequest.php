<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $itemsRaw = $this->input('items');
        if (is_string($itemsRaw)) {
            $decoded = json_decode($itemsRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['items' => $decoded]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'delivery_method_id' => ['required','integer','exists:delivery_methods,id'],
            'payment_method_id'  => ['required','integer','exists:payment_methods,id'],
            'customer_type'      => ['required','in:private,company'],
            'contact_name'       => ['required','string','max:255'],
            'phone'              => ['required','string','max:255'],
            'address'            => ['nullable','string','max:500'],

            'client_total'       => ['nullable','numeric'],

            'items'              => ['required','array','min:1'],
            'items.*.code'       => ['required','string','max:255'], // код = SKU (или slug, если так заполняешь)
            'items.*.qty'        => ['required','integer','min:1','max:9999'],
        ];
    }

    public function messages(): array
    {
        return ['items.required' => 'Корзина пуста.'];
    }
}
