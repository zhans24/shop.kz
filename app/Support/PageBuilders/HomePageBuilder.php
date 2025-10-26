<?php

namespace App\Support\PageBuilders;

use App\Models\Page;
use App\Support\Contracts\PageBuilder;

final class HomePageBuilder implements PageBuilder
{

    public function build(Page $page): array
    {
        $decor  = data_get($page->content, 'hero.decor_text');
        $slides = array_values(data_get($page->content, 'hero.slides', []) ?? []);

        $toUrl = function (?string $collection) use ($page) {
            if (!$collection) return null;
            $url = $page->getFirstMediaUrl($collection, 'webp');
            return $url ?: $page->getFirstMediaUrl($collection);
        };

        $out = [];
        foreach ($slides as $s) {
            $uid = $s['uid'] ?? null;
            $out[] = [
                'title'     => $s['title'] ?? '',
                'text'      => $s['text']  ?? '',
                'left_url'  => $toUrl($uid ? 'hero_left_'  . $uid : null),
                'right_url' => $toUrl($uid ? 'hero_right_' . $uid : null),
            ];
        }

        return [
            'template'   => (string) $page->template,
            'exists'     => true,
            'title'      => $page->title,
            'meta'       => [
                'title'       => $page->meta_title,
                'description' => $page->meta_description,
            ],
            'decor_text' => $decor,
            'slides'     => $out,
        ];
    }
}
