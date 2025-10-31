<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = 'new';

        Lead::create($data);

        return back()->with('ok', 'Спасибо! Заявка отправлена. Мы свяжемся с вами.');
    }
}
