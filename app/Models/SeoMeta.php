<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model {
    protected $casts = ['extra' => 'array'];
    public function seoable(){ return $this->morphTo(); }
}

/**@mixin
 *
 */

trait HasSeo {
    public function seo(){ return $this->morphOne(SeoMeta::class, 'seoable'); }
}

