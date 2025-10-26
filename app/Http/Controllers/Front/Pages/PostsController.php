<?php

namespace App\Http\Controllers\Front\Pages;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    // /news  (+ ?type=news|promo)
    public function index(Request $r)
    {
        $type = $r->get('type'); // null|news|promo

        $q = Post::query()->published()->with('seo')
            ->orderByDesc('published_at')->orderByDesc('id');

        if (in_array($type, ['news','promo'], true)) {
            $q->where('type', $type);
        }

        $items = $q->paginate(12)->withQueryString();

        // Заголовок под макет: «Новости и акции» (или «Новости», «Акции»)
        $pageTitle = match ($type) {
            'news'  => 'Новости',
            'promo' => 'Акции',
            default => 'Новости и акции',
        };

        return view('posts.index', compact('items','pageTitle','type'));
    }

    // /promotions → тот же список, но сразу promo
    public function promos(Request $r)
    {
        // прокидываем параметр type=promo
        $r->merge(['type' => 'promo']);
        return $this->index($r);
    }

    public function show(string $slug)
    {
        $post = Post::with('seo')->published()->where('slug', $slug)->firstOrFail();

        return view('posts.show', [
            'post'     => $post,
            'seoModel' => $post,
        ]);
    }
}
