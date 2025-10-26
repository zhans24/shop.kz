<?php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Support\PageData;

class AboutController extends Controller
{
    public function index()
    {
        $data = PageData::getByTemplate('about');
        abort_unless(($data['exists'] ?? false) === true, 404);
        return view('pages.about', $data);
    }
}
