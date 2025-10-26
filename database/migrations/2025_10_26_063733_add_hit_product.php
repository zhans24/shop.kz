<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // флаг «хит»
            $table->boolean('is_hit')
                ->default(false)
                ->after('is_published')
                ->index();

            // сортировка хитов (чем меньше — тем выше в списке)
            $table->unsignedInteger('hit_sort')
                ->nullable()
                ->after('is_hit')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_hit', 'hit_sort']);
        });
    }
};
