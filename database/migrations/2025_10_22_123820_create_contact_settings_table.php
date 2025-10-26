<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contact_settings', function (Blueprint $t) {
            $t->id();
            $t->string('company_name')->nullable();
            $t->text('company_text')->nullable();

            $t->json('phones')->nullable();
            $t->string('email')->nullable();

            $t->string('whatsapp')->nullable();
            $t->string('tiktok')->nullable();
            $t->string('instagram')->nullable();
            $t->string('youtube')->nullable();

            $t->text('address')->nullable();
            $t->text('map_embed')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contact_settings');
    }
};
