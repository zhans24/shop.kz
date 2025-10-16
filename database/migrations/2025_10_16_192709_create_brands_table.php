<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('brands', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('country')->nullable();
            $t->boolean('is_visible')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
            $t->index(['is_visible','sort']);
        });
    }
    public function down(): void { Schema::dropIfExists('brands'); }
};
