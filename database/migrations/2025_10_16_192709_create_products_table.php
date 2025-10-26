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
            $t->longText('description')->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->decimal('min_price', 12, 2)->nullable();
            $t->timestamps();

            $t->index(['category_id','is_published']);
            $t->index(['brand_id']);
            $t->index(['name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
