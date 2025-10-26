<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')
                ->nullable()
                ->comment('Процент скидки (1..100), null = нет скидки')
                ->after('price');

            $table->boolean('discount_is_forever')
                ->default(false)
                ->comment('Бессрочная скидка')
                ->after('discount_percent');

            $table->dateTime('discount_starts_at')
                ->nullable()
                ->comment('Старт скидки (UTC)')
                ->after('discount_is_forever');

            $table->dateTime('discount_ends_at')
                ->nullable()
                ->comment('Конец скидки (UTC), игнорируется если discount_is_forever = 1')
                ->after('discount_starts_at');

            $table->index(['discount_starts_at', 'discount_ends_at'], 'products_discount_period_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_discount_period_idx');
            $table->dropColumn([
                'discount_percent',
                'discount_is_forever',
                'discount_starts_at',
                'discount_ends_at',
            ]);
        });
    }
};
