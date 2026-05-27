<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('status')->default('pending');
    // pending | confirmed | processing | shipped | delivered | cancelled | refunded
    $table->string('payment_status')->default('unpaid');
    // unpaid | paid | partially_paid | refunded
    $table->string('payment_method')->nullable();
    $table->string('payment_id')->nullable(); // ID externo (MP, Stripe)
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('shipping_cost', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    // Datos del comprador (snapshot)
    $table->json('billing_address');
    $table->json('shipping_address');
    $table->text('notes')->nullable();
    $table->string('coupon_code')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamp('shipped_at')->nullable();
    $table->timestamp('delivered_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
