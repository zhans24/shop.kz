<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $t) {
            if (Schema::hasColumn('order_items', 'product_variant_id')) {
                try {
                    DB::statement('ALTER TABLE order_items DROP FOREIGN KEY order_items_product_variant_id_foreign');
                } catch (\Throwable $e) {
                }

                try {
                    $t->dropColumn('product_variant_id');
                } catch (\Throwable $e) {
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $t) {
            if (!Schema::hasColumn('order_items', 'product_variant_id')) {
                $t->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }
};
