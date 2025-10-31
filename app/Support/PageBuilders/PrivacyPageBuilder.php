<?php

namespace App\Support\PageBuilders;

use App\Models\Page;
use App\Support\Contracts\PageBuilder;

final class PrivacyPageBuilder implements PageBuilder
{
    public function build(Page $page): array
    {
        $c = $page->content ?? [];

        return [
            'template' => 'privacy',
            'exists'   => true,
            'title'    => $page->title ?: 'Политика конфиденциальности',
            'meta'     => [
                'title'       => $page->meta_title,
                'description' => $page->meta_description,
            ],
            'body_html'  => (string) data_get($c, 'privacy.body', ''),
        ];
    }

}
