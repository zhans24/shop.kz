<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('author_name')->nullable();
            $t->unsignedTinyInteger('rating'); // 1..5
            $t->text('body')->nullable();
            $t->json('photos')->nullable();
            $t->boolean('is_approved')->default(false);
            $t->timestamps();
            $t->index(['product_id','is_approved']);
        });
    }
    public function down(): void { Schema::dropIfExists('reviews'); }
};
