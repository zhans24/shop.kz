<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $t->string('slug')->unique();
            $t->string('name');
            $t->boolean('is_visible')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
            $t->index(['parent_id','is_visible','sort']);
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); }
};
