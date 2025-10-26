<?php

namespace App\Support\PageBuilders;

use App\Models\Page;
use App\Support\Contracts\PageBuilder;

final class DeliveryPageBuilder implements PageBuilder
{
    public function build(Page $page): array
    {
        $c = $page->content ?? [];

        $image = $page->getFirstMediaUrl('delivery_image', 'webp') ?: $page->getFirstMediaUrl('delivery_image');

        return [
            'template'    => 'delivery',
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

            'decor_text'  => data_get($c, 'delivery.decor_text'),
            'image'       => $image,
            'payment'     => [
                'title'  => data_get($c, 'delivery.title', $page->title), // "Доставка и оплата"
                'points' => array_values(data_get($c, 'delivery.points', [])), // [{text}]
            ],
        ];
    }
}
