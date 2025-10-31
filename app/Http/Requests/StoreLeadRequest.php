<?php

// app/Http/Requests/StoreLeadRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'    => ['required','string','max:100'],
            'phone'   => ['required','string','max:50'],
            'email'   => ['nullable','email','max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'имя',
            'phone' => 'телефон',
            'email' => 'email',
        ];
    }
}
