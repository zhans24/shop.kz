<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();
            $t->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('short_desc')->nullable();
            $t->longText('description')->nullable();
            $t->decimal('rating', 3, 1)->nullable(); // cached avg (e.g., 4.8)
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            // optional cached helpers
            $t->decimal('min_price', 12, 2)->nullable();
            $t->timestamps();
            $t->index(['category_id','is_published']);
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
