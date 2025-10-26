<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'meta_title', 'meta_description', 'h1',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }
}

