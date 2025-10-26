<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'delivery_method_id' => 'required|exists:delivery_methods,id',
            'payment_method_id'  => 'required|exists:payment_methods,id',
            'customer_type'      => 'required|in:private,company',
            'contact_name'       => 'required|string|max:255',
            'phone'              => 'required|string|max:50',
            'address'            => 'nullable|string|max:500',
            'client_total'       => 'nullable|numeric',
        ];
    }
}
