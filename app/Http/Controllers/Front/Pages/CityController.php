<?php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CityController extends Controller
{
    public function set(Request $request, string $slug)
    {
        $slug = Str::slug($slug);

        $page = Page::byTemplate('header');
        $cities = collect((array) data_get($page?->content, 'header.cities', []))
            ->map(fn ($r) => Str::slug((string) ($r['slug'] ?? $r['name'] ?? '')))
            ->filter()
            ->values();

        if ($cities->contains($slug)) {
            session(['city' => $slug]);
        }

        return back();
    }
}
