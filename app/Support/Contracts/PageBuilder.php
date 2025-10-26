<?php

namespace App\Support\Contracts;

use App\Models\Page;

interface PageBuilder
{
    public function build(Page $page): array;
}
