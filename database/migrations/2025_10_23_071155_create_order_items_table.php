<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id');
            $t->unsignedBigInteger('product_id')->nullable();
            $t->unsignedBigInteger('product_variant_id')->nullable();

            $t->string('sku', 100)->nullable();
            $t->string('name');
            $t->integer('qty')->default(1);
            $t->decimal('price', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);

            $t->timestamps();

            $t->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $t->index('sku');
            $t->unique(['order_id', 'sku']); // антидубли по SKU
        });
    }
    public function down(): void { Schema::dropIfExists('order_items'); }
};
