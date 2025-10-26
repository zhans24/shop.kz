<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('order_number', 32)->nullable()->unique();
            $t->string('status', 32)->default('new');

            $t->string('customer_type', 32)->default('private'); // private/company
            $t->string('contact_name')->nullable();
            $t->string('phone', 50)->nullable();
            $t->string('address')->nullable();

            $t->unsignedBigInteger('delivery_method_id')->nullable();
            $t->string('delivery_method_name')->nullable();
            $t->decimal('shipping_total', 12, 2)->default(0);

            $t->unsignedBigInteger('payment_method_id')->nullable();
            $t->string('payment_method_name')->nullable();

            $t->integer('items_count')->default(0);
            $t->decimal('items_subtotal', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);

            $t->dateTime('ordered_at')->nullable();
            $t->dateTime('paid_at')->nullable();

            $t->timestamps();

            $t->index('status');
            $t->index('ordered_at');
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
