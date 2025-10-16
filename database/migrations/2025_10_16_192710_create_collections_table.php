<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('collections', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique(); // hits, new, sale, custom-*
            $t->string('name');
            $t->enum('type', ['manual','rule'])->default('manual');
            $t->json('rule')->nullable(); // {"published_after":"2025-01-01", ...}
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('collections'); }
};
