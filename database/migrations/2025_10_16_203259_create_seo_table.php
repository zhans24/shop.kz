<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seo_meta', function (Blueprint $t) {
            $t->id();
            $t->morphs('seoable');
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->string('h1')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('seo_meta'); }
};
