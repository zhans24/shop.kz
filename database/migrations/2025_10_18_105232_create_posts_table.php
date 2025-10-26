<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->enum('type', ['news','promo'])->index(); // новость / акция
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('excerpt', 500)->nullable();
            $t->longText('content')->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable()->index();
            $t->json('data')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('posts'); }
};
