<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('delivery_methods', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->decimal('price', 10, 2)->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

    }
    public function down(): void { Schema::dropIfExists('delivery_methods'); }
};
