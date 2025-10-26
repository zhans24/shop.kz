<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;

trait HasSeo
{
    public function seo()
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function upsertSeo(array $data): void
    {
        $this->seo ? $this->seo->fill($data)->save() : $this->seo()->create($data);
    }
}
