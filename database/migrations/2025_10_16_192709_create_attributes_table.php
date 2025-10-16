<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attributes', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique(); // ram, storage, nfc ...
            $t->string('name');
            $t->enum('type', ['text','number','boolean','select']);
            $t->string('unit')->nullable(); // GB, mAh, ", Hz
            $t->boolean('is_filterable')->default(true);
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('attributes'); }
};
