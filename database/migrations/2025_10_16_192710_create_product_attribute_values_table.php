<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_attribute_values', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('attribute_id')->constrained()->cascadeOnDelete();


            $t->foreignId('attribute_value_id')->nullable()->constrained()->nullOnDelete();

            $t->string('value_text', 191)->nullable();
            $t->decimal('value_number', 12, 3)->nullable();
            $t->boolean('value_bool')->nullable();

            $t->timestamps();

            $t->unique(['product_id','attribute_id']);
            $t->index(['attribute_id','attribute_value_id'], 'pav_attr_attrval_idx');
            $t->index(['attribute_id','value_text'], 'pav_attr_text_idx');
            $t->index(['attribute_id','value_number'], 'pav_attr_num_idx');
            $t->index(['attribute_id','value_bool'], 'pav_attr_bool_idx');
        });

    }
    public function down(): void { Schema::dropIfExists('product_attribute_values'); }
};
