<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku'))  $table->string('sku', 64)->nullable()->after('description');
            if (!Schema::hasColumn('products', 'sort')) $table->unsignedInteger('sort')->nullable()->after('price');

            if (!Schema::hasColumn('products', 'discount_percent'))    $table->unsignedTinyInteger('discount_percent')->nullable()->after('price');
            if (!Schema::hasColumn('products', 'discount_is_forever')) $table->boolean('discount_is_forever')->default(false)->after('discount_percent');
            if (!Schema::hasColumn('products', 'discount_starts_at'))  $table->timestamp('discount_starts_at')->nullable()->after('discount_is_forever');
            if (!Schema::hasColumn('products', 'discount_ends_at'))    $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_ends_at'))    $table->dropColumn('discount_ends_at');
            if (Schema::hasColumn('products', 'discount_starts_at'))  $table->dropColumn('discount_starts_at');
            if (Schema::hasColumn('products', 'discount_is_forever')) $table->dropColumn('discount_is_forever');
            if (Schema::hasColumn('products', 'discount_percent'))    $table->dropColumn('discount_percent');

            if (Schema::hasColumn('products', 'sort')) $table->dropColumn('sort');
            if (Schema::hasColumn('products', 'sku'))  $table->dropColumn('sku');
        });
    }
};
