<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('sku')->unique();
            $t->decimal('price', 12, 2);
            $t->decimal('price_old', 12, 2)->nullable();
            $t->integer('quantity')->default(0);
            $t->json('options')->nullable(); // {"color":"Black","storage":"256GB"}
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
            $t->index(['product_id','is_active','sort']);
        });
    }
    public function down(): void { Schema::dropIfExists('product_variants'); }
};
