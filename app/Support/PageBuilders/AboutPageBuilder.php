<?php

namespace App\Support\PageBuilders;

use App\Models\Page;
use App\Support\Contracts\PageBuilder;

final class AboutPageBuilder implements PageBuilder
{
    public function build(Page $page): array
    {
        $c = $page->content ?? [];

        $image = null;
        $video = null;

        if ($m = $page->getFirstMedia('about_media')) {
            $mime = (string) $m->mime_type;

            if (str_starts_with($mime, 'image/')) {
                try {
                    $image = $m->hasGeneratedConversion('webp') ? $m->getUrl('webp') : $m->getUrl();
                } catch (\Throwable) {
                    $image = $m->getUrl();
                }
            } elseif (str_starts_with($mime, 'video/')) {
                $video = $m->getUrl();
            }
        }

        $reviews = [];
        foreach ((array) data_get($c, 'about.reviews', []) as $r) {
            $uid = $r['uid'] ?? null;
            $avatar = $uid ? ($page->getFirstMediaUrl('about_review_' . $uid, 'webp')
                ?: $page->getFirstMediaUrl('about_review_' . $uid)) : null;

            $reviews[] = [
                'name'   => $r['name'] ?? '',
                'text'   => $r['text'] ?? '',
                'avatar' => $avatar,
            ];
        }

        return [
            'template'    => 'about',
            'exists'      => true,
            'title'       => $page->title,
            'meta'        => [
                'title'       => $page->meta_title,
                'description' => $page->meta_description,
            ],
            'breadcrumbs' => [
                ['title' => 'Главная', 'url' => route('front.home')],
                ['title' => $page->title, 'url' => null],
            ],

            'decor_text'  => data_get($c, 'about.decor_text'),
            'image'       => $image, // string|null
            'video'       => $video, // string|null

            'about'       => [
                'title' => data_get($c, 'about.title', $page->title),
                'text'  => data_get($c, 'about.text'),
            ],
            'benefits'    => array_values(data_get($c, 'about.benefits', [])),
            'reviews'     => $reviews,
        ];
    }
}
