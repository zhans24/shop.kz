<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $t) {
            $t->id();

            // компания
            $t->string('company_name')->nullable();
            $t->text('company_text')->nullable();

            // контакты
            $t->json('phones')->nullable();     // массив [{raw, tel}], максимум 2
            $t->string('email')->nullable();

            // соцсети
            $t->string('whatsapp')->nullable();
            $t->string('facebook')->nullable();
            $t->string('tiktok')->nullable();
            $t->string('youtube')->nullable();
            $t->string('telegram')->nullable();

            // адрес и карта
            $t->text('address')->nullable();
            $t->text('map_embed')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
