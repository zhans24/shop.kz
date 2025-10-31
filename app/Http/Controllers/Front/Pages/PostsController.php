<?php
// app/Http/Controllers/Front/Pages/PostsController.php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $r)
    {
        $type = $r->get('type'); // null|news|promo

        $q = Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if (in_array($type, ['news', 'promo'], true)) {
            $q->where('type', $type);
        }

        $items = $q->paginate(6)->withQueryString();

        $pageTitleBase = match ($type) {
            'news'  => 'Новости',
            'promo' => 'Акции',
            default => 'Новости и акции',
        };

        $page = Page::query()
            ->where('template', 'news')
            ->with('seo')
            ->first();

        $seo = [
            'title' => $page?->seo?->meta_title ?: ($page?->meta_title ?: ($pageTitleBase . ' — TechnoStyle')),
            'desc'  => $page?->seo?->meta_description ?: ($page?->meta_description ?: 'Свежие новости и спецпредложения магазина TechnoStyle.'),
            'h1'    => $page?->seo?->h1 ?: $pageTitleBase,
        ];

        return view('pages.posts', [
            'items'       => $items,
            'pageTitle'   => $pageTitleBase,
            'seo'         => $seo,
            'type'        => $type,
        ]);
    }
}
