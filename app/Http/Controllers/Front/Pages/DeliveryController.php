<?php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Support\PageData;

class DeliveryController extends Controller
{
    public function index()
    {
        $data = PageData::getByTemplate('delivery');
        abort_unless(($data['exists'] ?? false) === true, 404);
        return view('pages.delivery', $data);
    }
}
